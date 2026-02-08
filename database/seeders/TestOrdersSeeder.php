<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Commande;
use App\Models\Produitcommande;
use App\Models\Client;
use App\Models\Produit;
use Carbon\Carbon;

class TestOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a test client
        $client = Client::where('email', 'client1@test.local')->first();
        if (!$client) {
            return; // No client, skip
        }

        // Get some products
        $produits = Produit::take(3)->get();
        if ($produits->isEmpty()) {
            return; // No products, skip
        }

        // Create a test order
        $commande = Commande::create([
            'DateCommande' => Carbon::now(),
            'Statut' => 'en cours',
            'MontantTotal' => 10000, // Example amount
            'Client_idClient' => $client->idClient,
        ]);

        // Add products to the order
        foreach ($produits as $produit) {
            Produitcommande::create([
                'Produit_idProduit' => $produit->idProduit,
                'Commande_idCommande' => $commande->idCommande,
                'Quantite' => 1,
                'PrixUnitaire' => $produit->Prix ?? 1000,
                'DateAjout' => Carbon::now(),
            ]);
        }
    }
}
