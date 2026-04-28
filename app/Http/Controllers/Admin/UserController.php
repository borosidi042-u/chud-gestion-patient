<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }
        $user->delete();
        return back()->with('success', 'Compte supprimé.');
    }
}
