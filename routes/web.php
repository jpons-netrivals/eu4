<?php

use App\Http\Controllers\ShipsController;
use App\Models\Asteroid;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\PHP;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $user = App\Models\User::first();
    $ship = $user->ships->first();
    return
        'Galaxy Location: ' . $ship->GalaxyX . ':' . $ship->GalaxyY .
        PHP_EOL . '<br />Solar System Location: ' .  $ship->SolarSystemX . ':' . $ship->SolarSystemY;
});

Route::get('/ship/find/of/{user}', [ShipsController::class, 'index']);
Route::get('/ship/move/to/ss/{ship}/{to_x}:{to_y}', [ShipsController::class, 'moveToSS']);
Route::get('/ship/time/for/movement/{ship}', [ShipsController::class, 'timeForAction']);
Route::get('/ship/can/move/{ship}', [ShipsController::class, 'canShipMove']);
Route::get('/ship/get/ss/visible/{user}', [ShipsController::class, 'getSSVisible']);
Route::get('/ship/is/on/asteroid/{ship}', [ShipsController::class, 'isOnAsteroid']);
Route::get('/ship/mine/{ship}', [ShipsController::class, 'doMining']);

# Planet routes
Route::get('/planet/show/base/{planet}', [\App\Http\Controllers\PlanetController::class, 'showBase']);
Route::get('/planet/check/seconds/between/last_check_and_now/{planet}', [\App\Http\Controllers\PlanetController::class, 'checkTimeBetweenNowAndLastCheck']);
Route::get('/planet/resource/to/add/count/{planet}', [\App\Http\Controllers\PlanetController::class, 'howManyResourcesToAdd']);
Route::get('/planet/add/resources/{planet}', [\App\Http\Controllers\PlanetController::class, 'addResources']);


# Solar System routes
Route::get('/galaxy/get/visible/ss/{user}', [\App\Http\Controllers\SolarSystemController::class, 'getVisibleSS']);

# Ship Builder routes
Route::get('/build/ship/{user}/{planet}', [\App\Http\Controllers\ShipBuilderController::class, 'buildShip']);


# Testing routes

Route::get('/test/{ship}', function(\App\Models\Ship $ship){
    $asteroid = Asteroid::where('asteroids.x', $ship->SolarSystemX)->where('asteroids.y', $ship->SolarSystemY)
        ->join('solar_systems', 'asteroids.SolarSystemID', '=', 'solar_systems.id')
        ->where('solar_systems.x', $ship->GalaxyX)
        ->where('solar_systems.y', $ship->GalaxyY)->get();

    dd(DB::getQueryLog());
    return $asteroid;
});
