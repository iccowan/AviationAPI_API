<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use DB;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Storage;

class UpdateChartsA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Update:ChartsA';

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

            $dttp = 'DDTPPA_'.$airac;
            $storage = base_path('/public/storage/temporary_storage/');

            $client = new Client;

            $res_a = $client->request('GET', 'https://aeronav.faa.gov/upload_313-d/terminal/'.$dttp.'.zip', ['sink' => $storage.$dttp]);
            Storage::put('/public/charts/AIRAC_'.$airac.'/'.$dttp.'.zip', $res_a->getBody());
            Storage::delete('/public/temporary_storage/'.$storage.$dttp);
        }
    }
}
