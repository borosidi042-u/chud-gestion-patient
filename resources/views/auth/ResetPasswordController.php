<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class PasswordResetController extends Controller
{
    // ═══════════════════════════════════════════════════════════════
    // ÉTAPE 1 — Formulaire : saisir l'email
    // ═══════════════════════════════════════════════════════════════
    public function showEmailForm()
    {
        return view('auth.forgot-email');
    }

    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email'    => 'L\'adresse email n\'est pas valide.',
            'email.exists'   => 'Aucun compte trouvé avec cet email.',
        ]);

        $email = strtolower(trim($request->email));

        // Générer un code à 4 chiffres
        $code = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        // Supprimer les anciens codes pour cet email
        DB::table('password_reset_codes')->where('email', $email)->delete();

        // Enregistrer le nouveau code (expire dans 10 minutes)
        DB::table('password_reset_codes')->insert([
            'email'      => $email,
            'code'       => $code,
            'expires_at' => Carbon::now()->addMinutes(10),
            'used'       => false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Envoyer l'email avec le code
        Mail::send('auth.emails.reset-code', ['code' => $code], function ($m) use ($email) {
            $m->to($email)
              ->subject('CHUD B/A — Code de réinitialisation de mot de passe');
        });

        // Stocker l'email en session pour les étapes suivantes
        session(['reset_email' => $email]);

        return redirect()->route('password.verify-code')
                         ->with('status', 'Un code à 4 chiffres a été envoyé à '.$email);
    }

    // ═══════════════════════════════════════════════════════════════
    // ÉTAPE 2 — Formulaire : saisir le code reçu
    // ═══════════════════════════════════════════════════════════════
    public function showCodeForm()
    {
        if (!session('reset_email')) {
            return redirect()->route('password.request')
                             ->with('error', 'Veuillez d\'abord entrer votre email.');
        }
        return view('auth.forgot-code', ['email' => session('reset_email')]);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:4'],
        ], [
            'code.required' => 'Le code est obligatoire.',
            'code.digits'   => 'Le code doit contenir exactement 4 chiffres.',
        ]);

        $email = session('reset_email');
        if (!$email) {
            return redirect()->route('password.request');
        }

        $record = DB::table('password_reset_codes')
                    ->where('email', $email)
                    ->where('code',  $request->code)
                    ->where('used',  false)
                    ->first();

        if (!$record) {
            return back()->withErrors(['code' => 'Code incorrect. Vérifiez votre email.']);
        }

        if (Carbon::parse($record->expires_at)->isPast()) {
            return back()->withErrors(['code' => 'Ce code a expiré (10 minutes). Recommencez.']);
        }

        // Marquer le code comme utilisé et passer à l'étape 3
        DB::table('password_reset_codes')
          ->where('email', $email)
          ->where('code',  $request->code)
          ->update(['used' => true]);

        // Stocker la validation en session
        session(['reset_verified' => true]);

        return redirect()->route('password.new-password')
                         ->with('status', 'Code vérifié ! Choisissez votre nouveau mot de passe.');
    }

    // Renvoyer un nouveau code
    public function resendCode(Request $request)
    {
        $email = session('reset_email');
        if (!$email) {
            return redirect()->route('password.request');
        }

        $code = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        DB::table('password_reset_codes')->where('email', $email)->delete();
        DB::table('password_reset_codes')->insert([
            'email'      => $email,
            'code'       => $code,
            'expires_at' => Carbon::now()->addMinutes(10),
            'used'       => false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Mail::send('auth.emails.reset-code', ['code' => $code], function ($m) use ($email) {
            $m->to($email)->subject('CHUD B/A — Nouveau code de réinitialisation');
        });

        return back()->with('status', 'Nouveau code envoyé à '.$email);
    }

    // ═══════════════════════════════════════════════════════════════
    // ÉTAPE 3 — Formulaire : saisir le nouveau mot de passe
    // ═══════════════════════════════════════════════════════════════
    public function showNewPasswordForm()
    {
        if (!session('reset_email') || !session('reset_verified')) {
            return redirect()->route('password.request')
                             ->with('error', 'Accès non autorisé. Recommencez le processus.');
        }
        return view('auth.forgot-reset', ['email' => session('reset_email')]);
    }

    public function updatePassword(Request $request)
    {
        if (!session('reset_email') || !session('reset_verified')) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required'  => 'Le nouveau mot de passe est obligatoire.',
            'password.min'       => 'Au moins 8 caractères requis.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        $email = session('reset_email');

        $user = User::where('email', $email)->first();
        if (!$user) {
            return redirect()->route('password.request')
                             ->with('error', 'Utilisateur introuvable.');
        }

        $user->update(['password' => Hash::make($request->password)]);

        // Nettoyer la session
        session()->forget(['reset_email', 'reset_verified']);

        return redirect()->route('login')
                         ->with('status', 'Mot de passe modifié avec succès ! Connectez-vous.');
    }
}
