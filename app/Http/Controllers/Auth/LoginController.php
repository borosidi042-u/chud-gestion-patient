<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    // ── Page de connexion ────────────────────────────────────────────────
    public function showLoginForm()
    {
        if (Auth::check()) return redirect()->route('dashboard');
        return view('auth.login');
    }

    // ── Traitement connexion ─────────────────────────────────────────────
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'email.required'    => 'L\'adresse email est obligatoire.',
            'email.email'       => 'L\'adresse email n\'est pas valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min'      => 'Au moins 6 caractères requis.',
        ]);

        $credentials = $request->only('email', 'password');

        // Vérifier d'abord si l'utilisateur existe et est approuvé
        $user = User::where('email', $credentials['email'])->first();

        if ($user) {
            if (!$user->approved) {
                return back()
                    ->withInput($request->only('email', 'remember'))
                    ->withErrors(['email' => 'Votre compte est en attente de validation par l\'administrateur.']);
            }
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))
                             ->with('success', 'Bienvenue, ' . Auth::user()->prenom . ' !');
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => 'Email ou mot de passe incorrect.']);
    }

    // ── Page d'inscription ───────────────────────────────────────────────
    public function showRegisterForm()
    {
        if (Auth::check()) return redirect()->route('dashboard');
        return view('auth.register');
    }

    // ── Traitement inscription → redirige vers LOGIN avec message validation ──
    public function register(Request $request)
    {
        $request->validate([
            'nom'      => ['required','string','max:100','regex:/^[\p{L}\s\-\']+$/u'],
            'prenom'   => ['required','string','max:100','regex:/^[\p{L}\s\-\']+$/u'],
            'email'    => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:8','confirmed'],
        ], [
            'nom.required'       => 'Le nom est obligatoire.',
            'nom.regex'          => 'Le nom ne doit contenir que des lettres.',
            'prenom.required'    => 'Le prénom est obligatoire.',
            'prenom.regex'       => 'Le prénom ne doit contenir que des lettres.',
            'email.required'     => 'L\'email est obligatoire.',
            'email.email'        => 'L\'email n\'est pas valide.',
            'email.unique'       => 'Cet email est déjà utilisé.',
            'password.required'  => 'Le mot de passe est obligatoire.',
            'password.min'       => 'Au moins 8 caractères requis.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        User::create([
            'nom'      => strtoupper(trim($request->nom)),
            'prenom'   => ucfirst(strtolower(trim($request->prenom))),
            'email'    => strtolower(trim($request->email)),
            'password' => Hash::make($request->password),
            'role'     => 'user',
            'approved' => false, // En attente de validation
        ]);

        // Redirige vers la page de connexion avec message d'attente
        return redirect()->route('login')
                         ->with('status', 'Votre compte a été créé. En attente de validation par l\'administrateur.');
    }

    // ── Déconnexion ──────────────────────────────────────────────────────
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
                         ->with('status', 'Vous avez été déconnecté avec succès.');
    }
}
