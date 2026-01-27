<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $primaryKey = 'idMessage';

    protected $fillable = [
        'Contenu',
        'DateEnvoi',
        'Statut',
        'Client_idClient',
        'Vendeur_idVendeur',
    ];

    public $timestamps = false; // car on utilise DateEnvoi

    public function client()
    {
        return $this->belongsTo(Client::class, 'Client_idClient');
    }

    public function vendeur()
    {
        return $this->belongsTo(Vendeur::class, 'Vendeur_idVendeur');
    }
}

