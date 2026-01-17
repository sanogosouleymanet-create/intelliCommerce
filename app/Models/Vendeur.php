<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Vendeur extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    protected $table = 'vendeurs';
    protected $primaryKey = 'idVendeur';
    public $incrementing = true;
    protected $keyType = 'int';
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
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    
    public function commandes()
    {
        return $this->hasMany(Commande::class);
    }
    public function getAuthPassword()
    {
        return $this->MotDePasse;
    }
}
