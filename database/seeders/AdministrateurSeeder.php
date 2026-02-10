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
            ['email' => 'akonate198@gmail.com'],
            [
                'Nom' => 'Konate',
                'Prenom' => 'Aboubacar',
                'MotDePasse' => Hash::make('konate123'),
                'DateCreation' => now(),
            ]
        );
    }
}
