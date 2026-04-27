<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // ── Page profil ──────────────────────────────────────────────────────
    public function index()
    {
        return view('profile.index', ['user' => Auth::user()]);
    }

    // ── Mettre à jour nom / prénom ───────────────────────────────────────
    public function updateInfo(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nom'    => ['required','string','max:100','regex:/^[\p{L}\s\-\']+$/u'],
            'prenom' => ['required','string','max:100','regex:/^[\p{L}\s\-\']+$/u'],
        ], [
            'nom.required'    => 'Le nom est obligatoire.',
            'nom.regex'       => 'Le nom ne doit contenir que des lettres.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'prenom.regex'    => 'Le prénom ne doit contenir que des lettres.',
        ]);

        $user->update([
            'nom'    => strtoupper(trim($request->nom)),
            'prenom' => ucfirst(strtolower(trim($request->prenom))),
        ]);

        return back()->with('success', 'Informations mises à jour avec succès.');
    }

    // ── Mettre à jour le mot de passe ────────────────────────────────────
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password'          => ['required', 'string'],
            'password'                  => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation'     => ['required'],
        ], [
            'current_password.required' => 'Le mot de passe actuel est obligatoire.',
            'password.required'         => 'Le nouveau mot de passe est obligatoire.',
            'password.min'              => 'Au moins 8 caractères requis.',
            'password.confirmed'        => 'Les mots de passe ne correspondent pas.',
        ]);

        // Vérifier l'ancien mot de passe
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.'])
                         ->with('tab', 'password'); // Rester sur l'onglet mot de passe
        }

        // Vérifier que le nouveau est différent de l'ancien
        if (Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Le nouveau mot de passe doit être différent de l\'actuel.'])
                         ->with('tab', 'password');
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Mot de passe modifié avec succès.');
    }
}