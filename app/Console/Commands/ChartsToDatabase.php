<?php

namespace App\Console\Commands;

use App\NextChart;
use Carbon\Carbon;
use Config;
use DB;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use SimpleXMLElement;

class ChartsToDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Update:ChartsToDatabase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Puts the charts in the database 7 days before the new cycle.';

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

            $client = new Client;

            $base_pdf_path = Config::get('app.charts_url').'/AIRAC_'.$airac.'/';
            $dtpp = $client->request('GET', Config::get('app.url').'/charts/AIRAC_'.$airac.'/DDTPPE_'.$airac.'/d-TPP_Metafile.xml');
            $charts_db = new SimpleXMLElement($dtpp->getBody());

            DB::table('charts_next')->truncate();

            foreach($charts_db[0]->state_code as $a) {
                $state_short = $a->attributes()->ID;
                $state_long = $a->attributes()->state_fullname;
                foreach($a[0] as $b) {
                    $city = $b->attributes()->ID;
                    $volume = $b->attributes()->volume;
                    foreach($b[0] as $c) {
                        $airport_name = $c->attributes()->ID;
                        $military = $c->attributes()->military;
                        $faa_ident = $c->attributes()->apt_ident;
                        $icao_ident = $c->attributes()->icao_ident;
                        foreach($c->record as $d) {
                            $chart_seq = $d->chartseq->__toString();
                            $chart_code = $d->chart_code->__toString();
                            $chart_name = $d->chart_name->__toString();
                            $chart_pdf = $d->pdf_name->__toString();
                            $chart_path = $base_pdf_path.$chart_pdf;

                            $chart = new NextChart;
                            $chart->state = $state_short;
                            $chart->state_full = $state_long;
                            $chart->city = $city;
                            $chart->volume = $volume;
                            $chart->airport_name = $airport_name;
                            $chart->military = $military;
                            $chart->faa_ident = $faa_ident;
                            $chart->icao_ident = $icao_ident;
                            $chart->chart_seq = $chart_seq;
                            $chart->chart_code = $chart_code;
                            $chart->chart_name = $chart_name;
                            $chart->pdf_name = $chart_pdf;
                            $chart->pdf_path = $chart_path;
                            $chart->save();
                        }
                    }
                }
            }
            $next->updated = 1;
            $next->save();
        }
    }
}
