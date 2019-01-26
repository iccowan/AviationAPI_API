<?php

namespace App\Console\Commands;

use App\Imports\AirportCordsImport;
use DB;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Storage;

class UpdateAirportCoords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Update:AirportCoords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the airport coordinates every year for VATSIM pilot distances.';

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
     * @return mixed
     */
    public function handle()
    {
        $client = new Client;
        $client->request('GET', 'http://ourairports.com/data/airports.csv', ['sink' => base_path('storage/app/public/airports.csv')]);

        DB::table('airport_coords')->truncate();
        Excel::import(new AirportCordsImport, '/public/airports.csv');

        Storage::delete('/public/airports.csv');
    }
}
