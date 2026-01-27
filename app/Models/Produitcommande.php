<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produitcommande extends Model
{
    use HasFactory;

    protected $table = 'Produitcommande';
    public $timestamps = false;

    protected $fillable = [
        'Produit_idProduit',
        'Commande_idCommande',
        'Quantite',
        'PrixUnitaire',
        'DateAjout',
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'Produit_idProduit');
    }

    public function commande()
    {
        return $this->belongsTo(Commande::class, 'Commande_idCommande');
    }
}
