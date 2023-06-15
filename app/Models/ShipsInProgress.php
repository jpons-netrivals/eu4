<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipsInProgress extends Model
{
    protected $table = 'ship_in_progress';
    protected $fillable = [
        'UserID',
        'will_be_finished_at',
        'has_finished',
        'config',
        'PlanetID',
    ];

    protected $casts = [
        'will_be_finished_at' => 'datetime',
        'config' => 'array',
    ];


}
