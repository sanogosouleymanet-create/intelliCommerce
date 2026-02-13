<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

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
        'sender_type',
    ];

    public $timestamps = false; // car on utilise DateEnvoi

    /**
     * Type casts for attributes.
     * Ensure DateEnvoi is treated as a datetime (Carbon instance).
     */
    protected $casts = [
        'DateEnvoi' => 'datetime',
    ];

    /**
     * Default attribute values to ensure new messages are created as unread.
     */
    protected $attributes = [
        'Statut' => 'non lu',
    ];

    /**
     * Scope a query to only include unread messages.
     */
    public function scopeUnread($query)
    {
        if (Schema::hasColumn('messages', 'Lu')) {
            return $query->where('Lu', false);
        }

        return $query->whereIn('Statut', ['envoye', 'non lu']);
    }

    /**
     * Mark this message as read and persist.
     */
    public function markAsRead()
    {
        if (Schema::hasColumn('messages', 'Lu')) {
            $this->Lu = true;
        } elseif (Schema::hasColumn('messages', 'Statut')) {
            $this->Statut = 'lu';
        }

        return $this->save();
    }

    /**
     * Return whether this message is considered unread.
     */
    public function isUnread()
    {
        if (Schema::hasColumn('messages', 'Lu')) {
            return !$this->Lu;
        }

        return in_array($this->Statut, ['envoye', 'non lu']);
    }

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

