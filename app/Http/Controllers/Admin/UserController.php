<?php

namespace App\Http\Controllers\Admin;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Afficher la liste des utilisateurs
    public function index() {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    // Changer le rôle d'un utilisateur
    public function changerRole(Request $request, $id) {
        $user = User::findOrFail($id);
        
        // Logique de bascule (Toggle)
        if($user->role === 'administrateur') {
            $user->role = 'utilisateur';
        } else {
            $user->role = 'administrateur';
        }
        
        $user->save();

        return back()->with('success', 'Le rôle de ' . $user->nom . ' a été mis à jour.');
    }
}
