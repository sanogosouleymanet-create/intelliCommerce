<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Produit;
use App\Observers\ProduitObserver;
use App\Models\Message;
use App\Observers\MessageObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Vendeur;
use App\Models\Client;
use App\Models\Administrateur;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Enregistrement des services
     */
    public function register(): void
    {
        //
    }

    /**
     * Méthode exécutée au démarrage de l'application
     * On y enregistre les Observers
     */
    public function boot(): void
    {
        // Associe l'Observer au modèle Produit
        Produit::observe(ProduitObserver::class);

        // Associe l'Observer au modèle Message
        Message::observe(MessageObserver::class);

        // Map short morph types (existing DB values) to full model classes
        Relation::morphMap([
            // messages and users — accept both lowercase and capitalized DB values
            'message' => Message::class,
            'Message' => Message::class,

            'vendeur' => Vendeur::class,
            'Vendeur' => Vendeur::class,

            'client' => Client::class,
            'Client' => Client::class,

            'administrateur' => Administrateur::class,
            'Administrateur' => Administrateur::class,
            // Some alerts/routes historically used 'admin' — map it too
            'admin' => Administrateur::class,
            'Admin' => Administrateur::class,
        ]);
    }
}
