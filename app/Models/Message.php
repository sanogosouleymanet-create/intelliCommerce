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
        'Administrateur_idAdministrateur',
    ];

    public $timestamps = false; // car on utilise DateEnvoi

    /**
     * Type casts for attributes.
     * Ensure DateEnvoi is treated as a datetime (Carbon instance).
     */
    protected $casts = [
        'DateEnvoi' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'Client_idClient');
    }

    public function vendeur()
    {
        return $this->belongsTo(Vendeur::class, 'Vendeur_idVendeur');
    }

    public function administrateur()
    {
        return $this->belongsTo(Administrateur::class, 'Administrateur_idAdministrateur');
    }
}

