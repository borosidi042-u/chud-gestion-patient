<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'admin') abort(403);
        $users = User::orderBy('created_at', 'desc')->get();

        // Séparer les utilisateurs en attente et approuvés
        $pendingUsers = $users->filter(fn($u) => !$u->approved && $u->id !== Auth::id());
        $approvedUsers = $users->filter(fn($u) => $u->approved || $u->id === Auth::id());

        return view('admin.users.index', compact('users', 'pendingUsers', 'approvedUsers'));
    }

    public function approve($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $user = User::findOrFail($id);
        if ($user->approved) {
            return back()->with('error', 'Ce compte est déjà validé.');
        }

        $user->update(['approved' => true]);
        return back()->with('success', 'Compte de ' . $user->prenom . ' ' . $user->nom . ' validé avec succès.');
    }

    public function disapprove($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas désapprouver votre propre compte.');
        }

        $user->update(['approved' => false]);
        return back()->with('success', 'Compte de ' . $user->prenom . ' ' . $user->nom . ' désactivé.');
    }

    public function changerRole(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        $request->validate(['role' => 'required|in:admin,user']);
        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas modifier votre propre rôle.');
        }
        $user->update(['role' => $request->role]);
        return back()->with('success', 'Rôle de ' . $user->prenom . ' ' . $user->nom . ' mis à jour.');
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $user = User::findOrFail($id);

        // Vérifier si l'utilisateur essaie de supprimer son propre compte
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                             ->with('error', '❌ Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Vérifier si l'utilisateur a des données liées (factures ou circuits)
        $facturesCount = $user->factures()->count();
        $circuitsCount = $user->circuits()->count();

        if ($facturesCount > 0 || $circuitsCount > 0) {
            $message = "⚠️ Impossible de supprimer le compte de {$user->prenom} {$user->nom} car il est lié à :";
            if ($facturesCount > 0) {
                $message .= "\n• {$facturesCount} facture(s) enregistrée(s)";
            }
            if ($circuitsCount > 0) {
                $message .= "\n• {$circuitsCount} passage(s) enregistré(s)";
            }
            $message .= "\n\n💡 Solution : Utilisez le bouton 'Transférer' pour déplacer ces données avant suppression.";

            return redirect()->route('admin.users.index')
                             ->with('error', $message);
        }

        // Si tout est OK, supprimer le compte
        try {
            $user->delete();
            return redirect()->route('admin.users.index')
                             ->with('success', "✅ Compte de {$user->prenom} {$user->nom} supprimé avec succès.");
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')
                             ->with('error', "❌ Erreur lors de la suppression : " . $e->getMessage());
        }
    }

    public function showTransferForm($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $oldUser = User::findOrFail($id);
        $users = User::orderBy('nom')->get();

        $facturesCount = $oldUser->factures()->count();
        $circuitsCount = $oldUser->circuits()->count();
        $patientsCount = 0; // Pas de relation directe, on met 0

        return view('admin.users.transfer', compact('oldUser', 'users', 'facturesCount', 'circuitsCount', 'patientsCount'));
    }

    public function transferData(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $request->validate([
            'new_user_id' => 'required|exists:users,id|different:old_user_id',
        ]);

        $oldUser = User::findOrFail($id);
        $newUser = User::findOrFail($request->new_user_id);

        if ($oldUser->id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas transférer les données de votre propre compte.');
        }

        DB::beginTransaction();
        try {
            // Transférer les factures
            $facturesTransferred = DB::table('factures')
                ->where('user_id', $oldUser->id)
                ->update(['user_id' => $newUser->id]);

            // Transférer les circuits
            $circuitsTransferred = DB::table('circuits')
                ->where('user_id', $oldUser->id)
                ->update(['user_id' => $newUser->id]);

            DB::commit();

            $message = "✅ Transfert effectué avec succès :";
            if ($facturesTransferred > 0) $message .= "\n• {$facturesTransferred} facture(s) transférée(s)";
            if ($circuitsTransferred > 0) $message .= "\n• {$circuitsTransferred} passage(s) transféré(s)";

            // Maintenant supprimer l'ancien utilisateur
            $oldUser->delete();

            return redirect()->route('admin.users.index')
                             ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors du transfert : ' . $e->getMessage());
        }
    }
}
