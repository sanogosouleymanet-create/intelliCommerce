<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produitcommande extends Model
{
    use HasFactory;

    protected $fillble = [
        'Produit_idProduit',
        'Commande_idCommande',
        'Quantite',	
        'PrixUnitaire',
    ];
}
