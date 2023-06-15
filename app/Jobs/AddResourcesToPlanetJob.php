<?php

namespace App\Jobs;

use App\Events\AddResourcesEvent;
use App\Models\Planet;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AddResourcesToPlanetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function __construct()
    {
    }

    public function handle(): void
    {
        $planets = Planet::whereNotNull('UserID')->get();
        foreach ($planets as $planet) {
            $this->addToPlanet($planet);
        }
    }

    public function addToPlanet(Planet $planet)
    {
        $resourcesToAdd = $this->howManyResourcesToAdd($planet);

        foreach ($resourcesToAdd as $resourceType => $number) {
            $planet->$resourceType = $planet->$resourceType + $number;
        }
        $planet->last_time_checked = DB::raw('CURRENT_TIMESTAMP');

        return $planet->save();
    }

    public function howManyResourcesToAdd(Planet $planet)
    {
        $secondsPassed = Carbon::now()->diffInSeconds($planet->last_time_checked);
        $result = [];
        $factoriesTypes = [
            'titanium',
            'copper',
            'iron',
            'aluminium',
            'silicon',
            'uranium',
            'nitrogen',
            'hydrogen',
        ];

        // todo make it dynamic

        foreach ($factoriesTypes as $factoriesType) {
            $level =  $planet->factories->where('type', $factoriesType)->first()->level;
            $multiplierName = $factoriesType . '_multiplier';
            $percentage = $planet->$multiplierName;
            $howMuchToAdd =  $secondsPassed * floatval('0.' . $percentage) * $level;
            $result[$factoriesType] = $howMuchToAdd;
        }

        return $result;
    }
}
