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
        'lu',
        'Expediteur_type',
        'Expediteur_id',
        'Message',
    ];

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
