<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Vérifier si l'admin existe déjà
        if (!User::where('email', 'admin@chud-ba.bj')->exists()) {
            User::create([
                'nom'      => 'ADMINISTRATEUR',
                'prenom'   => 'Chud',
                'email'    => 'admin@chud-ba.bj',
                'password' => Hash::make('Admin@2024'),
                'role'     => 'admin',
            ]);
            echo "✅ Compte admin créé : admin@chud-ba.bj / Admin@2024\n";
        } else {
            echo "ℹ️  Admin déjà existant.\n";
        }
    }
}