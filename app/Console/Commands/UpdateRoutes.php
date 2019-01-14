<?php

namespace App\Console\Commands;

use App\Routing;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use App\Imports\RoutingImport;
use Maatwebsite\Excel\Facades\Excel;
use Storage;

class UpdateRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Routes:Update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the routes in the database';

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
        $res = $client->request('GET', 'https://www.fly.faa.gov/rmt/data_file/prefroutes_db.csv');
        Storage::put('/public/routes_csv.csv', $res->getBody());

        DB::table('preferred_routes')->truncate();

        Excel::import(new RoutingImport, '/public/routes_csv.csv');

        Storage::delete('/public/routes_csv.csv');
    }
}
