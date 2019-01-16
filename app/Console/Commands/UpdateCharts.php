<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use DB;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class UpdateCharts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Update:Charts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the charts pack A 7 days before the new cycle.';

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
            $ddtpp_a = 'DDTPPA_'.$airac;
            $ddtpp_b = 'DDTPPB_'.$airac;
            $ddtpp_c = 'DDTPPC_'.$airac;
            $ddtpp_d = 'DDTPPD_'.$airac;
            $ddtpp_e = 'DDTPPE_'.$airac;
            $storage = base_path('/public/storage/charts/AIRAC_'.$airac.'/');

            $client = new Client;
            $client->request('GET', 'https://aeronav.faa.gov/upload_313-d/terminal/'.$ddtpp_a.'.zip', ['sink' => $storage.$ddtpp_a.'.zip']);
            $client = new Client;
            $client->request('GET', 'https://aeronav.faa.gov/upload_313-d/terminal/'.$ddtpp_b.'.zip', ['sink' => $storage.$ddtpp_b.'.zip']);
            $client = new Client;
            $client->request('GET', 'https://aeronav.faa.gov/upload_313-d/terminal/'.$ddtpp_c.'.zip', ['sink' => $storage.$ddtpp_c.'.zip']);
            $client = new Client;
            $client->request('GET', 'https://aeronav.faa.gov/upload_313-d/terminal/'.$ddtpp_d.'.zip', ['sink' => $storage.$ddtpp_d.'.zip']);
            $client = new Client;
            $client->request('GET', 'https://aeronav.faa.gov/upload_313-d/terminal/'.$ddtpp_e.'.zip', ['sink' => $storage.$ddtpp_e.'.zip']);
        }
    }
}
