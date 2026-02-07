<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Client;
use App\Models\Vendeur;
use Carbon\Carbon;

class TestAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 test clients
        for ($i = 1; $i <= 10; $i++) {
            $email = "client{$i}@test.local";
            Client::firstOrCreate(
                ['email' => $email],
                [
                    'Nom' => "Client{$i}",
                    'Prenom' => "Test{$i}",
                    'DateDeNaissance' => Carbon::parse('1990-01-01')->addDays($i)->toDateString(),
                    'Adresse' => "Adresse test {$i}",
                    'TelClient' => 600000000 + $i,
                    'MotDePasse' => Hash::make('1234'),
                    'DateCreation' => Carbon::now(),
                ]
            );
        }

        // Create 10 test vendeurs
        for ($i = 1; $i <= 10; $i++) {
            $email = "vendeur{$i}@test.local";
            Vendeur::firstOrCreate(
                ['email' => $email],
                [
                    'Nom' => "Vendeur{$i}",
                    'Prenom' => "Test{$i}",
                    'Adresse' => "Adresse boutique {$i}",
                    'TelVendeur' => 700000000 + $i,
                    'email' => $email,
                    'NomBoutique' => "boutique{$i}",
                    'Statut' => 'actif',
                    'MotDePasse' => Hash::make('1234'),
                    'DateCreation' => Carbon::now(),
                ]
            );
        }
    }
}
