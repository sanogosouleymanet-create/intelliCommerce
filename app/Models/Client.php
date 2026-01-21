<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
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
    ];
    public function commandes()
    {
        return $this->hasMany(Commande::class);
    }
    public function message()
    {
        return $this->hasMany(Message::class);
    }
}
