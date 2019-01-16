<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use DB;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Chumper\Chumper\Zipper;

class ExtractCharts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Update:ChartsExtract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extracts the charts 7 days before the new cycle.';

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

            $dttp_a = 'DDTPPA_'.$airac;
            $dttp_b = 'DDTPPB_'.$airac;
            $dttp_c = 'DDTPPC_'.$airac;
            $dttp_d = 'DDTPPD_'.$airac;
            $dttp_e = 'DDTPPE_'.$airac;

            \Zipper::make(base_path('storage/app/public/charts/AIRAC_'.$airac.'/'.$dttp_a.'.zip'))->extractTo(base_path('public/charts/AIRAC_'.$airac));

            \Zipper::make(base_path('storage/app/public/charts/AIRAC_'.$airac.'/'.$dttp_b.'.zip'))->extractTo(base_path('public/charts/AIRAC_'.$airac));

            \Zipper::make(base_path('storage/app/public/charts/AIRAC_'.$airac.'/'.$dttp_c.'.zip'))->extractTo(base_path('public/charts/AIRAC_'.$airac));

            \Zipper::make(base_path('storage/app/public/charts/AIRAC_'.$airac.'/'.$dttp_d.'.zip'))->extractTo(base_path('public/charts/AIRAC_'.$airac));

            \Zipper::make(base_path('storage/app/public/charts/AIRAC_'.$airac.'/'.$dttp_e.'.zip'))->extractTo(base_path('public/charts/AIRAC_'.$airac.'/'.$dttp_e));

        }
    }
}
