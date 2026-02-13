<?php

namespace App\Observers;

use App\Models\Produit;
use App\Services\IAService;

class ProduitObserver
{
    /*
     Méthode exécutée automatiquement après la création d’un produit
     Elle vérifie immédiatement si le stock est faible.
     */
    public function created(Produit $produit)
    {
        // Appel du service IA
        app(IAService::class)->verifierStockProduit($produit);
    }

    /*
     Méthode exécutée automatiquement après la mise à jour d’un produit
     Elle vérifie :
     Le stock et L’historique des ventes
     */
    public function updated(Produit $produit)
    {
        // Vérification du stock
        app(IAService::class)->verifierStockProduit($produit);

        // Vérification des ventes anciennes
        app(IAService::class)->verifierProduitPeuVendu($produit);
    }
}
