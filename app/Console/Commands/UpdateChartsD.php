<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use DB;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Storage;

class UpdateChartsD extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Update:ChartsD';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the charts pack D 7 days before the new cycle.';

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

        $now = Carbon::now();
        $next = DB::table('chart_update_cycles')->where('updated', 0)->first();
        $next_cycle = Carbon::create('20'.$next->year, $next->month, $next->day);
        $time_until_next = $now->diffInDays($next_cycle);
        if($time_until_next = 7) {
            $airac = $next->year.$next->month.$next->day;
            $dttp = 'DDTPPD_'.$airac;
            $storage = base_path('/public/storage/charts/');

            $client = new Client;
            $res_a = $client->request('GET', 'https://aeronav.faa.gov/upload_313-d/terminal/'.$dttp.'.zip', ['sink' => $storage.$dttp.'.zip']);
        }
    }
}
