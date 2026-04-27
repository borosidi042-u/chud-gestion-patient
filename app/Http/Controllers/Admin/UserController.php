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
        $users = User::orderBy('nom')->get();
        return view('admin.users.index', compact('users'));
    }

    public function changerRole(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        $request->validate(['role' => 'required|in:admin,user']);
        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) {
            return back()->with('error','Vous ne pouvez pas modifier votre propre rôle.');
        }
        $user->update(['role' => $request->role]);
        return back()->with('success','Rôle de '.$user->prenom.' '.$user->nom.' mis à jour.');
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) {
            return back()->with('error','Vous ne pouvez pas supprimer votre propre compte.');
        }
        $user->delete();
        return back()->with('success','Compte supprimé.');
    }
}
