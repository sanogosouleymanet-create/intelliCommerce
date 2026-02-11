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
        'montant_total',
        'MontantTotal',
        'Client_idClient',
        'Vendeur_idVendeur',
    ];

    protected $table = 'commandes';
    protected $primaryKey = 'idCommande';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $casts = [
        'DateCommande' => 'datetime',
    ];

    public function getMontantTotalAttribute()
    {
        return $this->attributes['MontantTotal'] ?? $this->attributes['montant_total'] ?? 0;
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'Client_idClient');
    }

    public function produit()
    {
        return $this->belongsToMany(Produit::class, 'Produitcommande', 'Commande_idCommande', 'Produit_idProduit')->withPivot('Quantite', 'PrixUnitaire');
    }

    public function produitcommandes()
    {
        return $this->hasMany(\App\Models\Produitcommande::class, 'Commande_idCommande');
    }

    public function vendeur()
    {
        return $this->belongsTo(Vendeur::class, 'Vendeur_idVendeur');
    }


}
