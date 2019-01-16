<?php

namespace App\Console\Commands;

use App\CurrentChart;
use App\NextChart;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class PurgeChartDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Update:PurgeChartDatabase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs the day a chart cycle is to be released.';

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
        $next = DB::table('chart_update_cycles')->where('updated', 1)->first();
        $now = Carbon::now();
        $next_cycle = Carbon::create('20'.$next->year, $next->month, $next->day);
        $time_until_next = $now->diffInDays($next_cycle);

        if($time_until_next != 0) {
            DB::table('charts_current')->truncate();
            $next_charts = NextChart::get();

            foreach($next_charts as $n) {
                $c = new CurrentChart;
                $c->state = $n->state;
                $c->state_full = $n->state_full;
                $c->city = $n->city;
                $c->volume = $n->volume;
                $c->airport_name = $n->airport_name;
                $c->military = $n->military;
                $c->faa_ident = $n->faa_ident;
                $c->icao_ident = $n->icao_ident;
                $c->chart_seq = $n->chart_seq;
                $c->chart_code = $n->chart_code;
                $c->chart_name = $n->chart_name;
                $c->pdf_name = $n->pdf_name;
                $c->pdf_path = $n->pdf_path;
                $c->save();
            }

            DB::table('chart_update_cycles')->where('updated', 1)->delete();
        }
    }
}
