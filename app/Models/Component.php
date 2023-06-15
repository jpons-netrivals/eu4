<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    protected $casts = [
        'Costs' => 'array',
        'Features' => 'array',
    ];
}
