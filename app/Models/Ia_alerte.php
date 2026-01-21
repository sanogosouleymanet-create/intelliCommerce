<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'idAdmi',
    ];

    public function administrateur()
    {
        return $this->belongsTo(Administrateur::class, 'idAdmi');
    }
}
