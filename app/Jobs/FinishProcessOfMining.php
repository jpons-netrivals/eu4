<?php

namespace App\Jobs;

use App\Events\AddResourcesEvent;
use App\Models\Asteroid;
use App\Models\Move;
use App\Models\Planet;
use App\Models\Ship;
use App\Models\ShipsInProgress;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class FinishProcessOfMining implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $move;

    public function __construct(Move $move)
    {
        $this->move = $move;
    }

    public function handle(): void
    {
        if ($this->move->has_finished == 0){
            $this->move->update(['has_finished' => 1]);
            $ship = $this->move->ship;
            $previousShipConfig = $ship->config;
            \Log::debug('Previous Ship config');
            \Log::debug($previousShipConfig);
            $asteroid = Asteroid::where('asteroids.x', $ship->SolarSystemX)->where('asteroids.y', $ship->SolarSystemY)
                ->join('solar_systems', 'asteroids.SolarSystemID', '=', 'solar_systems.id')
                ->where('solar_systems.x', $ship->GalaxyX)
                ->where('solar_systems.y', $ship->GalaxyY)->first();


            \Log::debug($asteroid);
            $asteroidResources = $asteroid->config;
            $newShipConfig = $previousShipConfig;
            if (!array_key_exists('resources', $newShipConfig)) {
                $newShipConfig['resources'] = [];
            }
            if (!array_key_exists('resources', $previousShipConfig)) {
                $previousShipConfig['resources'] = [];
            }

            foreach ($asteroidResources as $resourceName => $quantity) {
                if ($quantity > 0) {
                    # generate by AI, review it later
//                    if ($previousShipConfig['resources'][$resourceName] + $quantity > $previousShipConfig['capacity']) {
//                        $quantity = $previousShipConfig['capacity'] - $previousShipConfig['resources'][$resourceName];
//                    }
                    if (!array_key_exists($resourceName, $previousShipConfig['resources'])) {
                        $newShipConfig['resources'][$resourceName] = 0;
                    }
                    $newShipConfig['resources'][$resourceName] += $quantity;
                    $asteroidResources[$resourceName] = 0;
                }
            }

            \Log::debug('New Ship config');
            \Log::debug($newShipConfig);

            $ship->update([
                'config' => $newShipConfig
            ]);
        }
    }
}
