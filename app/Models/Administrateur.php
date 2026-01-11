<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrateur extends Model
{
    use HasFactory;

    protected $fillable = [
            "idAdmi",
            "Nom",	
            "Prenom",
            "email",
            "MotDePasse",
            "DateCreation",
    ];
     public function Admin()
    {
        return $this->hasMany(Ia_alerte::class, 'Administrateur_idAdmi');
    }
}
