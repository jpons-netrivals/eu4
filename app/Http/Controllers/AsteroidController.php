<?php

namespace App\Http\Controllers;

use App\Models\Asteroid;
use Illuminate\Http\Request;

class AsteroidController extends Controller
{
    public function index()
    {
        return Asteroid::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'SolarSystemID' => ['required', 'integer'],
            'x' => ['required', 'integer'],
            'y' => ['required', 'integer'],
            'config' => ['required'],
        ]);

        return Asteroid::create($request->validated());
    }

    public function show(Asteroid $asteroid)
    {
        return $asteroid;
    }

    public function update(Request $request, Asteroid $asteroid)
    {
        $request->validate([
            'SolarSystemID' => ['required', 'integer'],
            'x' => ['required', 'integer'],
            'y' => ['required', 'integer'],
            'config' => ['required'],
        ]);

        $asteroid->update($request->validated());

        return $asteroid;
    }

    public function destroy(Asteroid $asteroid)
    {
        $asteroid->delete();

        return response()->json();
    }
}
