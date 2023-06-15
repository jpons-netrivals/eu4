<?php

namespace App\Jobs;

use App\Events\AddResourcesEvent;
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

class FinishProcessOfBuildingShip implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $shipInProgress;

    public function __construct(ShipsInProgress $shipInProgress)
    {
        $this->shipInProgress = $shipInProgress;
    }

    public function handle(): void
    {
        if ($this->shipInProgress->has_finished == 0){
            $this->shipInProgress->update(['has_finished' => 1]);

            $planet = Planet::find($this->shipInProgress->PlanetID);
            Ship::create([
                'UserID' => $this->shipInProgress->UserID,
                'PlanetID' => $this->shipInProgress->PlanetID,
                'SolarSystemX' => $planet->x,
                'SolarSystemY' => $planet->y,
                'GalaxyX' => $planet->solar_system->x,
                'GalaxyY' => $planet->solar_system->y,
                'config' => $this->shipInProgress->config,
            ]);
        }
    }
}
