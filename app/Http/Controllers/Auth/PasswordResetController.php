<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

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

        // LOG DE DÉBOGAGE - Affiche le code dans les logs (À SUPPRIMER EN PRODUCTION)
        Log::info("Code de réinitialisation pour {$email} : {$code}");

        // Pour le développement : stocker le code en session pour l'afficher dans la vue
        session(['debug_code' => $code]);

        // Envoyer l'email avec le code (uniquement si configuré pour l'envoi réel)
        try {
            Mail::send('auth.emails.reset-code', ['code' => $code], function ($m) use ($email) {
                $m->to($email)
                  ->subject('CHUD B/A — Code de réinitialisation de mot de passe');
            });

            // Vérifier si l'email a bien été envoyé (pour le driver log)
            if (config('mail.default') === 'log') {
                Log::info("Email écrit dans les logs storage/logs/laravel.log");
            }
        } catch (\Exception $e) {
            // En cas d'erreur d'envoi, on log l'erreur mais on continue (mode développement)
            Log::error("Erreur d'envoi d'email : " . $e->getMessage());

            // En mode développement, on avertit l'utilisateur
            if (config('app.debug')) {
                return back()->with('warning', "Mode développement : Impossible d'envoyer l'email. Code: {$code}")
                             ->with('debug_code', $code);
            }
        }

        // Stocker l'email en session pour les étapes suivantes
        session(['reset_email' => $email]);

        // Supprimer l'ancienne vérification si elle existe
        session()->forget('reset_verified');

        return redirect()->route('password.verify-code')
                         ->with('status', 'Un code à 4 chiffres a été envoyé à ' . $email);
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

        // Récupérer le code de débogage s'il existe
        $debugCode = session('debug_code');

        return view('auth.forgot-code', [
            'email' => session('reset_email'),
            'debug_code' => $debugCode
        ]);
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
            return redirect()->route('password.request')
                             ->with('error', 'Session expirée. Veuillez recommencer.');
        }

        $record = DB::table('password_reset_codes')
                    ->where('email', $email)
                    ->where('code',  $request->code)
                    ->where('used',  false)
                    ->first();

        if (!$record) {
            // En mode développement, proposer d'afficher le code
            if (config('app.debug') && session('debug_code')) {
                return back()->withErrors(['code' => 'Code incorrect. Code de débogage : ' . session('debug_code')]);
            }
            return back()->withErrors(['code' => 'Code incorrect. Vérifiez votre email.']);
        }

        if (Carbon::parse($record->expires_at)->isPast()) {
            return back()->withErrors(['code' => 'Ce code a expiré (10 minutes). Recommencez.']);
        }

        // Marquer le code comme utilisé
        DB::table('password_reset_codes')
          ->where('email', $email)
          ->where('code',  $request->code)
          ->update(['used' => true]);

        // Stocker la validation en session
        session(['reset_verified' => true]);

        // Supprimer le code de débogage
        session()->forget('debug_code');

        return redirect()->route('password.new-password')
                         ->with('status', 'Code vérifié ! Choisissez votre nouveau mot de passe.');
    }

    // Renvoyer un nouveau code
    public function resendCode(Request $request)
    {
        $email = session('reset_email');
        if (!$email) {
            return redirect()->route('password.request')
                             ->with('error', 'Session expirée. Veuillez recommencer.');
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

        // Log et session de débogage
        Log::info("Nouveau code de réinitialisation pour {$email} : {$code}");
        session(['debug_code' => $code]);

        try {
            Mail::send('auth.emails.reset-code', ['code' => $code], function ($m) use ($email) {
                $m->to($email)
                  ->subject('CHUD B/A — Nouveau code de réinitialisation');
            });
        } catch (\Exception $e) {
            Log::error("Erreur d'envoi d'email : " . $e->getMessage());
            if (config('app.debug')) {
                return back()->with('warning', "Mode développement : Nouveau code: {$code}")
                             ->with('debug_code', $code);
            }
        }

        return back()->with('status', 'Nouveau code envoyé à ' . $email);
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
            return redirect()->route('password.request')
                             ->with('error', 'Session invalide. Veuillez recommencer le processus.');
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

        // Vérifier que le nouveau mot de passe est différent de l'ancien
        if (Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Le nouveau mot de passe doit être différent de l\'ancien.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        // Nettoyer complètement la session
        session()->forget(['reset_email', 'reset_verified', 'debug_code']);

        // Supprimer tous les codes utilisés pour cet email
        DB::table('password_reset_codes')->where('email', $email)->delete();

        return redirect()->route('login')
                         ->with('status', 'Mot de passe modifié avec succès ! Connectez-vous.');
    }
}
