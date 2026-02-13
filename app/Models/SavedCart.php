<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedCart extends Model
{
    protected $table = 'Panier';

    protected $fillable = [
        'guard',
        'user_id',
        'cart',
    ];

    protected $casts = [
        'cart' => 'array',
    ];
}
