<?php

namespace App\Http\Controllers;

use App\Models\Component;
use App\Models\Planet;
use App\Models\ShipsInProgress;
use App\Models\ShipsType;
use App\Models\User;
use Illuminate\Http\Request;

class ShipBuilderController extends Controller
{
    public function buildShip(User $user, Planet $planet, Request $request)
    {
        $requiredResources = [];

        $ship = ShipsType::find($request->ShipTypeID);
        $shipCosts = $ship->Costs;
        $shipCosts = json_decode($shipCosts, true);
        foreach ($shipCosts as $resource => $amount) {
            $requiredResources[$resource] = $amount;
        }

        $components = Component::whereIn('id', $request->Components)->get();
        $componentsSumCells = 0;
        $componentsFormatted = [];
        foreach ($components as $component) {
            $componentsFormatted[$component['id']] = $component;
        }


        foreach ($request->Components as $components) {
            $componentsCost = $componentsFormatted[$components];
            foreach ($componentsCost['Costs'] as $materialName => $costs) {
                $requiredResources[$materialName] = array_key_exists($materialName, $requiredResources) ? $requiredResources[$materialName] + $costs : $costs;
            }
            $componentsSumCells += $componentsCost->CellsRequired;
        }


        dd($componentsSumCells);
        if ($ship->Cells < $componentsSumCells) {
            return 'Not enough cells';
        }
        if (!$this->doesThePlanetHaveEnoughResources($planet, $requiredResources)) {
            return 'Not enough resources';
        }
        ShipsInProgress::create([
            'UserID' => $user->id,
            'PlanetID' => $planet->id,
            'config' => [
                'ShipTypeID' => $ship->id,
                'Components' => $request->Components,
            ],
            'will_be_finished_at' => now()->addMinutes($this->calculateTimeToBuildShip($ship, $components)),
        ]);
        return 'Time to build in minutes: ' . $this->calculateTimeToBuildShip($ship, $components);
    }

    private function doesThePlanetHaveEnoughResources($planet, $requiredResources)
    {
        foreach ($requiredResources as $resource => $amount) {
            if ($planet->$resource < $amount) {
                return false;
            }
        }
        return true;
    }

    public function calculateTimeToBuildShip($ship, $components)
    {
        $time = $ship->TimeToBuild;
        foreach ($components as $component) {
            $time += $component->TimeToBuild;
        }
        return $time;
    }
}
