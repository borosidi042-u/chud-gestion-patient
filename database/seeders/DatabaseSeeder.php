<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Création des comptes Admin pour vous deux
        User::create([
            'nom' => 'Ton_Nom', 
            'prenom' => 'Ton_Prenom',
            'email' => 'admin@chud.bj',
            'role' => 'admin',
            'password' => Hash::make('password'), // Mot de passe par défaut
            
        ]);

        User::create([
            'nom' => 'Nom_Binome',
            'prenom' => 'Prenom_Binome',
            'email' => 'binome@chud.bj',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 2. Création des services du CHUD Borgou-Alibori
        $services = [
            ['nom_service' => 'Urgences', 'description' => 'Accueil des cas critiques'],
            ['nom_service' => 'Pédiatrie', 'description' => 'Soins infantiles'],
            ['nom_service' => 'Maternité', 'description' => 'Gynécologie et accouchements'],
            ['nom_service' => 'Radiologie', 'description' => 'Examens imagerie'],
            ['nom_service' => 'Laboratoire', 'description' => 'Analyses médicales'],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}