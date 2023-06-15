<?php

namespace App\Console\Commands;

use App\Jobs\AddResourcesToPlanetJob;
use App\Jobs\CloseMoves;
use App\Jobs\FinishProcessOfMining;
use App\Jobs\MakeSSVisible;
use App\Jobs\ProcessPodcast;
use App\Jobs\FinishProcessOfBuildingShip;
use App\Models\Move;
use App\Models\ShipsInProgress;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CloseThingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'moves:close';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will be executed by crone and will close things add resources to planet and more things';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
//        CloseMoves::dispatch();
        MakeSSVisible::dispatch();
        AddResourcesToPlanetJob::dispatch();
        $shipsInProgress = ShipsInProgress::where('has_finished', false)->get();
        foreach ($shipsInProgress as $shipInProgress) {
            $timeToDelay = Carbon::now()->diffInMinutes($shipInProgress->will_be_finished_at, true);
            \Log::info($timeToDelay);
            dispatch(new FinishProcessOfBuildingShip($shipInProgress))->delay(Carbon::now()->addMinutes($timeToDelay));
        }
        $movesInProgress = Move::where('has_arrived', false)->get();
        foreach ($movesInProgress as $moveInProgress) {
            $timeToDelay = Carbon::now()->diffInMinutes($moveInProgress->will_be_finished_at, true);
            \Log::info($timeToDelay);
            dispatch(new FinishProcessOfMining($moveInProgress))->delay(Carbon::now()->addMinutes($timeToDelay));
        }

        return 0;
    }


}
