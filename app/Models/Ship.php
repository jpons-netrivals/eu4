<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ship extends Model
{
    use HasFactory;

    protected $casts = [
        'config' => 'array',
    ];

    protected $fillable = ['UserID', 'ShipTypeID', 'GalaxyX', 'GalaxyY', 'SolarSystemX', 'SolarSystemY'];

    public function type()
    {
        return $this->belongsTo(ShipsType::class, 'ShipTypeID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID');
    }

    public function moves()
    {
        return $this->hasMany(Move::class, 'ShipID');
    }
}
