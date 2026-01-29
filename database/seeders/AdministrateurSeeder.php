<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Administrateur;
use Illuminate\Support\Facades\Hash;

class AdministrateurSeeder extends Seeder
{
    public function run()
    {
        // Change credentials after first login
        Administrateur::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'Nom' => 'Super',
                'Prenom' => 'Admin',
                'MotDePasse' => Hash::make('secret123'),
                'DateCreation' => now(),
            ]
        );
    }
}
