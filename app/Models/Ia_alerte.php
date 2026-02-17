<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Ia_alerte extends Model
{
    use HasFactory;

    protected $table = 'ia_alertes';
    protected $primaryKey = 'idAlerte';
    public $timestamps = false; // car DateCreation est manuelle

    protected $fillable = [
        'TypeAlerte',
        'Description',
        'DateCreation',
        'NiveauGravité',
        'destinataire_type',
        'destinataire_id',
        'Statut',
        'Expediteur_type',
        'Expediteur_id',
        'Message',
    ];

    /**
     * Default attribute values.
     */
    protected $attributes = [
        'Statut' => 'non lu',
    ];

    /**
     * Scope a query to only include unread alerts.
     */
    public function scopeUnread($query)
    {
        // Check for Statut column first (new), fallback to lu (old)
        if (Schema::hasColumn('ia_alertes', 'Statut')) {
            return $query->where('Statut', 'non lu');
        }
        
        // Fallback to old lu boolean column
        return $query->where('lu', false);
    }

    /**
     * Scope a query to only include read alerts.
     */
    public function scopeRead($query)
    {
        // Check for Statut column first (new), fallback to lu (old)
        if (Schema::hasColumn('ia_alertes', 'Statut')) {
            return $query->where('Statut', 'lu');
        }
        
        // Fallback to old lu boolean column
        return $query->where('lu', true);
    }

    /**
     * Mark this alert as read and persist.
     */
    public function markAsRead()
    {
        if (Schema::hasColumn('ia_alertes', 'Statut')) {
            $this->Statut = 'lu';
        } elseif (Schema::hasColumn('ia_alertes', 'lu')) {
            $this->lu = true;
        }

        return $this->save();
    }

    /**
     * Return whether this alert is considered unread.
     */
    public function isUnread()
    {
        if (Schema::hasColumn('ia_alertes', 'Statut')) {
            return $this->Statut === 'non lu';
        }
        
        return isset($this->lu) && !$this->lu;
    }

    public function destinataire()
    {
        return $this->morphTo();
    }

    public function source()
    {
        // Support both old (source_type/source_id) and new (Expediteur_type/Expediteur_id) column names
        $typeColumn = Schema::hasColumn($this->getTable(), 'Expediteur_type') ? 'Expediteur_type' : 'source_type';
        $idColumn = Schema::hasColumn($this->getTable(), 'Expediteur_id') ? 'Expediteur_id' : 'source_id';
        return $this->morphTo('source', $typeColumn, $idColumn);
    }
}
