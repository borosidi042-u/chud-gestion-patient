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
        $request->validate(['role' => 'required|in:admin,user,infirmier']);
        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas modifier votre propre rôle.');
        }
        $user->update(['role' => $request->role]);
        return back()->with('success', 'Rôle de ' . $user->prenom . ' ' . $user->nom . ' mis à jour.');
    }

    /**
     * Supprimer un utilisateur et transférer automatiquement ses données à l'admin
     */
    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $user = User::findOrFail($id);

        // Vérifier si l'utilisateur essaie de supprimer son propre compte
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                             ->with('error', '❌ Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Récupérer l'administrateur qui effectue la suppression
        $adminUser = Auth::user();

        DB::beginTransaction();
        try {
            $mouvementsTransferred = 0;
            $circuitsTransferred = 0;
            $patientsTransferred = 0;

            // Transférer les mouvements (table mouvements, colonne agent_id)
            $mouvementsTransferred = DB::table('mouvements')
                ->where('agent_id', $user->id)
                ->update(['agent_id' => $adminUser->id]);

            // Transférer les circuits (table circuits, colonne user_id)
            $circuitsTransferred = DB::table('circuits')
                ->where('user_id', $user->id)
                ->update(['user_id' => $adminUser->id]);

            // Transférer les patients (table patients, colonne user_id)
            $patientsTransferred = DB::table('patients')
                ->where('user_id', $user->id)
                ->update(['user_id' => $adminUser->id]);

            // Supprimer l'utilisateur
            $user->delete();

            DB::commit();

            // Construire le message de succès
            $message = "✅ Compte de {$user->prenom} {$user->nom} supprimé avec succès.";
            $details = [];
            if ($mouvementsTransferred > 0) $details[] = "{$mouvementsTransferred} mouvement(s)";
            if ($circuitsTransferred > 0) $details[] = "{$circuitsTransferred} circuit(s)";
            if ($patientsTransferred > 0) $details[] = "{$patientsTransferred} patient(s)";

            if (!empty($details)) {
                $message .= " Les données suivantes ont été transférées à votre compte : " . implode(', ', $details) . ".";
            } else {
                $message .= " Aucune donnée n'était associée à ce compte.";
            }

            return redirect()->route('admin.users.index')
                             ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.users.index')
                             ->with('error', "❌ Erreur lors de la suppression : " . $e->getMessage());
        }
    }

    /**
     * Afficher le formulaire de transfert de données (gardé pour compatibilité, mais plus nécessaire)
     */
    public function showTransferForm($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $oldUser = User::findOrFail($id);
        $users = User::orderBy('nom')->get();

        // Compter les données liées
        $circuitsCount = $oldUser->circuits()->count();
        $mouvementsCount = $oldUser->mouvements()->count();
        $patientsCount = $oldUser->patients()->count();

        return view('admin.users.transfer', compact('oldUser', 'users', 'circuitsCount', 'mouvementsCount', 'patientsCount'));
    }

    /**
     * Transférer les données d'un utilisateur à un autre (gardé pour compatibilité)
     */
    public function transferData(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $request->validate([
            'new_user_id' => 'required|exists:users,id',
        ]);

        $oldUser = User::findOrFail($id);
        $newUser = User::findOrFail($request->new_user_id);

        if ($oldUser->id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas transférer les données de votre propre compte.');
        }

        DB::beginTransaction();
        try {
            // Transférer les mouvements
            $mouvementsTransferred = DB::table('mouvements')
                ->where('agent_id', $oldUser->id)
                ->update(['agent_id' => $newUser->id]);

            // Transférer les circuits
            $circuitsTransferred = DB::table('circuits')
                ->where('user_id', $oldUser->id)
                ->update(['user_id' => $newUser->id]);

            // Transférer les patients
            $patientsTransferred = DB::table('patients')
                ->where('user_id', $oldUser->id)
                ->update(['user_id' => $newUser->id]);

            DB::commit();

            $message = "✅ Transfert effectué avec succès :";
            if ($mouvementsTransferred > 0) $message .= "\n• {$mouvementsTransferred} mouvement(s) transféré(s)";
            if ($circuitsTransferred > 0) $message .= "\n• {$circuitsTransferred} circuit(s) transféré(s)";
            if ($patientsTransferred > 0) $message .= "\n• {$patientsTransferred} patient(s) transféré(s)";

            return redirect()->route('admin.users.index')
                             ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors du transfert : ' . $e->getMessage());
        }
    }
}
