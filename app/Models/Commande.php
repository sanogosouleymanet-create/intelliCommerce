<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Vendeur;

class Commande extends Model
{
    use HasFactory;

    protected $fillable = [
        'DateCommande',
        'Statut',
        'MontantTotal',
        'Client_idClient',
        'Vendeur_idVendeur',
    ];

    protected $table = 'commandes';
    protected $primaryKey = 'idCommande';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    public function Client()
    {
        return $this->belongsTo(Client::class, 'Client_idClient');
    }

    public function Produit()
    {
        return $this->belongsToMany(Produit::class, 'Produitcommande', 'Commande_idCommande', 'Produit_idProduit')->withPivot('Quantite', 'PrixUnitaire');
    }

    public function Vendeur()
    {
        return $this->belongsTo(Vendeur::class, 'Vendeur_idVendeur');
    }


}
