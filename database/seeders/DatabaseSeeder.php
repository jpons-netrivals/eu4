<?php

namespace Database\Seeders;

use App\Models\Asteroid;
use App\Models\Component;
use App\Models\Factory;
use App\Models\Galaxy;
use App\Models\SolarSystem;
use App\Models\Planet;
use App\Models\ResourcesType;
use App\Models\PlanetsResource;
use App\Models\Ship;
use App\Models\ShipsType;
use App\Models\SSVisible;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    private int $boardSize = 10;

    private $resourcesTypes = [
        'titanium',
        'copper',
        'iron',
        'aluminium',
        'silicon',
        'uranium',
        'nitrogen',
        'hydrogen',
    ];

    private $resourcesTypesIDs = [];

    /**
     * Seed the application's database.
     * SS == solar system
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(10)->create();

        Galaxy::create([
            'name' => 'Galaxy1'
        ]);
        echo 'Galaxy created' . PHP_EOL;
        $this->createSolarSystems();
        echo 'Solar Systems created' . PHP_EOL;
        $this->createResourcesTypes();
        echo 'Resources types created' . PHP_EOL;
        $this->setResourcesPercentagesToEachPlanet();
        echo 'Resources added to the planets' . PHP_EOL;
        $this->createShipTypes();
        echo 'Ship types created' . PHP_EOL;
        $this->createComponents();
        echo 'Components created' . PHP_EOL;
        $this->createAsteroids();
        echo 'Asteroids created' . PHP_EOL;

        // add 2 sondas to the first user
        Ship::create([
            'UserID' => 1,
            'ShipTypeID' => 1,
            'SolarSystemX' => rand(0, 60),
            'SolarSystemY' => rand(0, 60),
            'GalaxyX' => 0,
            'GalaxyY' => 0,
        ]);
        Ship::create([
            'UserID' => 1,
            'ShipTypeID' => 3,
            'SolarSystemX' => 10,
            'SolarSystemY' => 10,
            'GalaxyX' => 0,
            'GalaxyY' => 0,
            'config' => [
                "Components" => [0, 1, 3, 3, 5  ],
            ]
        ]);

        // assign first planet to first user
        $firstPlanet = Planet::first();
        $firstPlanet->UserID = 1;
        $firstPlanet->titanium_multiplier = rand(5, 99);
        $firstPlanet->copper_multiplier = rand(5, 99);
        $firstPlanet->iron_multiplier = rand(5, 99);
        $firstPlanet->aluminium_multiplier = rand(5, 99);
        $firstPlanet->silicon_multiplier = rand(5, 99);
        $firstPlanet->uranium_multiplier = rand(5, 99);
        $firstPlanet->nitrogen_multiplier = rand(5, 99);
        $firstPlanet->hydrogen_multiplier = rand(5, 99);
        $firstPlanet->save();


        $this->createFactories();
        SSVisible::create([
            'UserID' => 1,
            'SolarSystemID' => $firstPlanet->solar_system->id
        ]);
    }

    private function createSolarSystems()
    {
        for ($x = 0; $x < $this->boardSize; $x++) {
            for ($y = 0; $y < $this->boardSize; $y++) {
                $solarSystem = SolarSystem::create([
                    'GalaxyID' => 1,
                    'x' => $x,
                    'y' => $y
                ]);
                $this->createPlanets($solarSystem->id);
            }
        }
    }

    private function createPlanets($solarSystemID)
    {
        $planetsInThisSS = rand(5, 8);
        $usedX = [];
        $usedY = [];
        for ($i = 0; $i <= $planetsInThisSS; $i++) {

            $x = rand(0, 60);
            $y = rand(0, 60);

            while (in_array($x, $usedX) && in_array($y, $usedY)) {
                $x = rand(0, 60);
                $y = rand(0, 60);
            }

            $usedX[] = $x;
            $usedY[] = $y;

            Planet::create([
                'SolarSystemID' => $solarSystemID,
                'UserID' => null,
                'x' => $x,
                'y' => $y,
                'titanium' => 1000,
                'copper' => 1000,
                'iron' => 1000,
                'aluminium' => 1000,
                'silicon' => 1000,
                'uranium' => 1000,
                'nitrogen' => 1000,
                'hydrogen' => 1000,
            ]);
        }
    }

    private function createResourcesTypes()
    {

        foreach ($this->resourcesTypes as $resourceType) {
            $ry = ResourcesType::create([
                'name' => $resourceType
            ]);
            $this->resourcesTypesIDs[] = $ry->id;
        }
    }

    private function setResourcesPercentagesToEachPlanet()
    {
        $planets = Planet::all();

        foreach ($planets as $planet) {
            foreach ($this->resourcesTypesIDs as $resourcesTypesID) {
                PlanetsResource::create([
                    'PlanetID' => $planet->id,
                    'ResourceTypeID' => $resourcesTypesID,
                    'Percentage' => rand(20, 99)
                ]);
            }
        }
    }

    public function createFactories()
    {
        $planets = User::first()->planets;
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

        foreach ($planets as $planet) {
            foreach ($factoriesTypes as $factoriesType) {

                Factory::create([
                    'PlanetID' => $planet->id,
                    'type' => $factoriesType,
                    'level' => 1
                ]);
            }
        }
    }

    public function createShipTypes()
    {
        $shipTypes = [
            [
                'Name' => 'Sonda',
                'NormalSpeed' => 10,
                'WrapSpeed' => 10,
                'Costs' => json_encode([
                    'titanium' => 100,
                ]),
                'TimeToBuild' => 5,
                'Cells' => 1
            ],
            [
                'Name' => 'Corvete',
                'NormalSpeed' => 10,
                'WrapSpeed' => 5,
                'Costs' => json_encode([
                    'titanium' => 1000,
                    'copper' => 1500,
                    'iron' => 500,
                ]),
                'TimeToBuild' => 10,
                'Cells' => 20
            ],
            [
                'Name' => 'Nave de carga',
                'NormalSpeed' => 10,
                'WrapSpeed' => 4,
                'Costs' => json_encode([
                    'titanium' => 1000,
                    'copper' => 1500,
                    'aluminium' => 800,
                ]),
                'TimeToBuild' => 15,
                'Cells' => 30
            ],
            [
                'Name' => 'Cruise',
                'NormalSpeed' => 7,
                'WrapSpeed' => 2,
                'Costs' => json_encode([
                    'titanium' => 4000,
                    'copper' => 3000,
                    'aluminium' => 1500,
                    'uranium' => 1000,
                    'silicon' => 500,
                ]),
                'TimeToBuild' => 20,
                'Cells' => 50
            ],
        ];
        foreach ($shipTypes as $shipType) {
            ShipsType::create($shipType);
        }
    }

    public function createComponents()
    {
        $components = [
            [
                'Name' => 'Fly Engine 1',
                'Costs' => ([
                    'titanium' => 1000,
                    'copper' => 500,
                    'iron' => 500,
                ]),
                'TimeToBuild' => 10,
                'CellsRequired' => 5,
                'Features' => ([
                    'NormalSpeed' => 2,
                ]),
            ],
            [
                'Name' => 'Fly Engine 2',
                'Costs' => ([
                    'titanium' => 2000,
                    'copper' => 1000,
                    'iron' => 1000,
                ]),
                'TimeToBuild' => 20,
                'CellsRequired' => 10,
                'Features' => ([
                    'NormalSpeed' => 4,
                ]),
            ],
            [
                'Name' => 'Wrap Engine 1',
                'Costs' => ([
                    'titanium' => 2000,
                    'copper' => 1000,
                    'iron' => 1000,
                ]),
                'TimeToBuild' => 20,
                'CellsRequired' => 10,
                'Features' => ([
                    'WrapSpeed' => 4,
                ]),
            ],
            [
                'Name' => 'Wrap Engine 2',
                'Costs' => ([
                    'titanium' => 4000,
                    'copper' => 2000,
                    'iron' => 2000,
                ]),
                'TimeToBuild' => 25,
                'CellsRequired' => 20,
                'Features' => ([
                    'WrapSpeed' => 8,
                ]),
            ],
            [
                'Name' => 'Asteroid Miner Engine 1',
                'Costs' => ([
                    'titanium' => 4000,
                    'copper' => 2000,
                    'iron' => 2000,
                ]),
                'TimeToBuild' => 1,
                'CellsRequired' => 5,
                'Features' => ([
                    'AsteroidMining' => 500,
                    'ResourcesCapacity' => 1000,
                ]),
            ],
            [
                'Name' => 'Cargo 1',
                'Costs' => ([
                    'titanium' => 4000,
                    'copper' => 2000,
                    'iron' => 2000,
                ]),
                'TimeToBuild' => 1,
                'CellsRequired' => 5,
                'Features' => ([
                    'ResourcesCapacity' => 20000,
                ]),
            ],
        ];

        foreach ($components as $component) {
            Component::create($component);
        }
    }

    public function createAsteroids()
    {
        Asteroid::create([
            'SolarSystemID' => 2,
            'x' => 10,
            'y' => 10,
            'config' => [
                'nitrogen' => 1000,
                'hydrogen' => 1000,
            ]
        ]);
    }
}
