<?php

namespace App\Http\Controllers;

use App\Models\Asteroid;
use App\Models\Component;
use App\Models\Move;
use App\Models\Ship;
use App\Models\SolarSystem;
use App\Models\SSVisible;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShipsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {

        $cordinates = [];
        foreach ($user->ships as $ship) {
            $cordinates = [
                'x' => $ship->GalaxyX,
                'y' => $ship->GalaxyY,
            ];
        }
        return view('welcome', [
            'cordinates' => $cordinates
        ]);
    }

    public function moveToSS(Ship $ship, Request $request)
    {
        Move::create([
            'UserID' => 1, // todo: refactor this
            'ShipID' => $ship->id,
            'started_at' => Carbon::now()->toDateTimeString(),
            'will_be_finished_at' => $this->timeForAction($ship, $request),
            'GalaxyX' => $request->GalaxyX,
            'GalaxyY' => $request->GalaxyY,
            'SSX' => 10,
            'SSY' => 10,
            'config' => [
                'type' => 'move',
            ]
        ]);
    }

    public function sumSameComponentFeaturesOfShip(Ship $ship, $componentFeatureName)
    {

        $shipComponentsIDs = $ship->config['Components'];
        $components = Component::whereIn('id', $shipComponentsIDs)->get();
        $enginesSpeed = [];
        foreach ($components as $component) {
            if (array_key_exists($componentFeatureName, $component['Features'])) {
                $enginesSpeed[$component->id] = $component['Features'][$componentFeatureName];
            }
        }
        $shipComponentsSpeed = 0;

        foreach ($shipComponentsIDs as $shipComponentsID) {
            if (array_key_exists($shipComponentsID, $enginesSpeed)) {
                $shipComponentsSpeed += $enginesSpeed[$shipComponentsID];
            }
        }

        return $shipComponentsSpeed;

    }

    public function timeForAction(Ship $ship, Request $request)
    {
        $minJumps = $this->findMinimumJumps([$ship->GalaxyX, $ship->GalaxyY], [$request->GalaxyX, $request->GalaxyY], 10);
        $timeForEachSS = 40; // minutes
        $current_timestamp = Carbon::now()->toDateTimeString(); // Produces something like 1552296328


        $wrapSpeed = $this->sumSameComponentFeaturesOfShip($ship, 'WrapSpeed');

        $timeOfTravel = ($minJumps * $timeForEachSS) / ($ship->type->WrapSpeed + $wrapSpeed);
        $later = Carbon::now()->addMinutes($timeOfTravel)->toDateTimeString();
        return $later;
    }

    public function canShipMove(Ship $ship)
    {
        return Carbon::now()->diffInSeconds($ship->moves->first()->will_be_finished_at, false) < 0;
    }

    public function isOnAsteroid(Ship $ship)
    {
        return Asteroid::where('asteroids.x', $ship->SolarSystemX)->where('asteroids.y', $ship->SolarSystemY)
            ->join('solar_systems', 'asteroids.SolarSystemID', '=', 'solar_systems.id')
            ->where('solar_systems.x', $ship->GalaxyX)
            ->where('solar_systems.y', $ship->GalaxyY)->count() == 1;
    }

    public function doMining(Ship $ship)
    {
        if (!$this->isOnAsteroid($ship)) {
            return 'You are not on an asteroid';
        }
        $asteroid = Asteroid::where('asteroids.x', $ship->SolarSystemX)->where('asteroids.y', $ship->SolarSystemY)
            ->join('solar_systems', 'asteroids.SolarSystemID', '=', 'solar_systems.id')
            ->where('solar_systems.x', $ship->GalaxyX)
            ->where('solar_systems.y', $ship->GalaxyY)->first();


        $shipMiningSpeed = $this->sumSameComponentFeaturesOfShip($ship, 'AsteroidMining');

        $asteroidSize = array_sum($asteroid->config);

        $miningTime = $asteroidSize / $shipMiningSpeed;
//        return Carbon::now()->addMinutes($miningTime)->toDateTimeString();
//        $this->sumSameComponentFeaturesOfShip($ship, 'ResourcesCapacity')/
        ;

        Move::create([
            'UserID' => 1, // todo: refactor this
            'ShipID' => $ship->id,
            'started_at' => Carbon::now()->toDateTimeString(),
            'will_be_finished_at' => Carbon::now()->addMinutes($miningTime)->toDateTimeString(),
            'GalaxyX' => $ship->GalaxyX,
            'GalaxyY' => $ship->GalaxyY,
            'SSX' => 10,
            'SSY' => 10,
            'config' => [
                'type' => 'asteroid_mining',
            ]
        ]);
    }

    public function getSSVisible(User $user)
    {
        return $user->ssVisible;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Ship  $ship
     * @return \Illuminate\Http\Response
     */
    public function show(Ship $ship)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Ship  $ship
     * @return \Illuminate\Http\Response
     */
    public function edit(Ship $ship)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ship  $ship
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ship $ship)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ship  $ship
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ship $ship)
    {
        //
    }

    private function findMinimumJumps($start, $end, $boardSize)
    {
        // Verificar si el punto de inicio y el punto final están dentro del rango del tablero
        if (
            $start[0] < 0 || $start[0] >= $boardSize || $start[1] < 0 || $start[1] >= $boardSize ||
            $end[0] < 0 || $end[0] >= $boardSize || $end[1] < 0 || $end[1] >= $boardSize
        ) {
            return -1; // Puntos fuera de rango
        }

        // Matriz de visitados para realizar el seguimiento de los nodos visitados
        $visited = array_fill(0, $boardSize, array_fill(0, $boardSize, false));

        // Cola para almacenar los nodos a visitar
        $queue = new \SplQueue();

        // Agregar el punto de inicio a la cola
        $queue->enqueue([$start, 0]); // [coordenada, saltos]

        // Marcar el punto de inicio como visitado
        $visited[$start[0]][$start[1]] = true;

        // Direcciones posibles: arriba, abajo, izquierda, derecha
        $directions = [[-1, 0], [1, 0], [0, -1], [0, 1]];

        // Bucle principal del BFS
        while (!$queue->isEmpty()) {
            // Obtener el siguiente nodo de la cola
            [$current, $jumps] = $queue->dequeue();

            // Verificar si hemos llegado al punto final
            if ($current[0] == $end[0] && $current[1] == $end[1]) {
                return $jumps; // Devolver el número de saltos
            }

            // Explorar las direcciones adyacentes
            foreach ($directions as $dir) {
                $nextX = $current[0] + $dir[0];
                $nextY = $current[1] + $dir[1];

                // Verificar si la siguiente posición es válida y no ha sido visitada
                if (
                    $nextX >= 0 && $nextX < $boardSize && $nextY >= 0 && $nextY < $boardSize &&
                    !$visited[$nextX][$nextY]
                ) {
                    // Marcar la siguiente posición como visitada y agregarla a la cola
                    $visited[$nextX][$nextY] = true;
                    $queue->enqueue([[$nextX, $nextY], $jumps + 1]);
                }
            }
        }

        return -1; // No se encontró un camino válido
    }
}
