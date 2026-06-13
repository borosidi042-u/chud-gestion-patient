public function run(): void
{
    \App\Models\User::create([
        'name' => 'Administrateur CHUD',
        'email' => 'borosidi042@gmail.com', // L'email pour te connecter
        'password' => bcrypt('administrateur'),   // Ton mot de passe secret
        'role' => 'admin',    // Ajuste si ta colonne s'appelle autrement
        
    ]);
}
