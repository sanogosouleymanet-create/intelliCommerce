<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;

    protected $fillable = [
        'idProduit',
        'Nom',
        'Description',
        'Prix',
        'Stock',
        'Categorie',
        'DateAjout',
        'Image',
        'Vendeur_idVendeur',
    ];
    protected $table = 'produits';
    protected $primaryKey = 'idProduit';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;
    public function vendeur()
    {
        return $this->belongsTo(Vendeur::class, 'Vendeur_idVendeur');
    }

    public function commandes()
    {
        return $this->belongsToMany(Commande::class, 'Produitcommande', 'Produit_idProduit', 'Commande_idCommande')->withPivot('Quantite');
    }
}
