<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Administrateur extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $table = 'administrateurs';
    protected $primaryKey = 'idAdmi';
    public $timestamps = false;

    protected $fillable = [
        "idAdmi",
        "Nom",
        "Prenom",
        "email",
        "MotDePasse",
        "DateCreation",
    ];

    // Return the password field used by the auth system
    public function getAuthPassword()
    {
        return $this->MotDePasse;
    }

}