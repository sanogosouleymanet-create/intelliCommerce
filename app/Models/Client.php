<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Client extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $table = 'clients';
    protected $primaryKey = 'idClient';
    public $timestamps = false;
    protected $fillable = [
        "idClient",
        "Nom",
        "Prenom",
        "DateDeNaissance",
        "Adresse",
        "TelClient",
        "email",
        "MotDePasse",
        "DateCreation",
        "Bloque",
    ];

    public function commandes()
    {
        return $this->hasMany(Commande::class, 'Client_idClient', 'idClient');
    }
    public function message()
    {
        return $this->hasMany(Message::class, 'Client_idClient', 'idClient');
    }

    public function getAuthPassword()
    {
        return $this->MotDePasse;
    }
}
