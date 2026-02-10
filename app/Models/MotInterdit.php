<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotInterdit extends Model
{
    protected $table = 'mots_interdits';
    protected $primaryKey = 'idMot';
    public $timestamps = false;

    protected $fillable = [
        'mot',
        'poids',
    ];
}
