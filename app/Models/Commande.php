<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $fillable = [
        'idCommande',
        'DateCommande',
        'Statut',
        'MontantTotal',
        'Utilisateurs_id',
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
        return $this->belongsToMany(Produit::class, 'Produitcommande', 'Commande_idCommande', 'Produit_idProduit')->withPivot('Quantite');
    }
}
