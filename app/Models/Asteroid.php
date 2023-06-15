<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asteroid extends Model
{
    protected $fillable = [
        'SolarSystemID',
        'x',
        'y',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
    ];
}
