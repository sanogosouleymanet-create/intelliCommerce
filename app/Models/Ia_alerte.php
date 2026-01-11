<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ia_alerte extends Model
{
     use HasFactory;
    protected $fillable = [
        'idAlerte',
        'TypeAlerte',
        'Description',
        'DateCreation',
        'NiveauGravité',
        'Utilisateurs_id',
        'Administrateur_idAdmi',
    ];

    public function Admin()
    {
        return $this->belongsTo(Administrateur::class, 'Administrateur_idAdmi');
    }
}
