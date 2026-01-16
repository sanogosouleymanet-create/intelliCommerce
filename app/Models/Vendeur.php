<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendeur extends Model
{
    protected $table = 'vendeurs';
    use HasFactory;
    /**
     * Disable automatic timestamps (created_at, updated_at)
     * because the `vendeurs` table does not have these columns.
     */
    public $timestamps = false;
    protected $fillable =[
        'idVendeur',
        'Nom',	
        'Prenom',	
        'Adresse',	
        'TelVendeur',	
        'email',	
        'NomBoutique',
        'Statut',
        'MotDePasse',
        'DateCreation',
    ];

    public function produits()
    {
        return $this->hasMany(Produit::class);
    }
    public function message()
    {
        return $this->hasMany(Message::class);
    }
}
