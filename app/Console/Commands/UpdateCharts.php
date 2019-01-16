<?php

namespace App\Console\Commands;

use App\NextChart;
use Carbon\Carbon;
use Config;
use DB;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use SimpleXMLElement;
use Storage;
use Chumper\Chumper\Zipper;

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
    protected $description = 'Updates the charts 7 days before the new cycle.';

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
            $storage = base_path('/public/storage/temporary_storage/');

            $client = new Client;

            $res_a = $client->request('GET', 'https://aeronav.faa.gov/upload_313-d/terminal/'.$dttp_a.'.zip', ['sink' => $storage.$dttp_a]);
            Storage::put('/public/charts/AIRAC_'.$airac.'/'.$dttp_a.'.zip', $res_a->getBody());
            \Zipper::make(base_path('storage/app/public/charts/AIRAC_'.$airac.'/'.$dttp_a.'.zip'))->extractTo(base_path('public/charts/AIRAC_'.$airac));
            Storage::delete('/public/storage/temporary_storage/'.$storage.$dttp_a);

            $res_b = $client->request('GET', 'https://aeronav.faa.gov/upload_313-d/terminal/'.$dttp_b.'.zip', ['sink' => $storage.$dttp_b]);
            Storage::put('/public/charts/AIRAC_'.$airac.'/'.$dttp_b.'.zip', $res_b->getBody());
            \Zipper::make(base_path('storage/app/public/charts/AIRAC_'.$airac.'/'.$dttp_b.'.zip'))->extractTo(base_path('public/charts/AIRAC_'.$airac));
            Storage::delete('/public/storage/temporary_storage/'.$storage.$dttp_b);

            $res_c = $client->request('GET', 'https://aeronav.faa.gov/upload_313-d/terminal/'.$dttp_c.'.zip', ['sink' => $storage.$dttp_c]);
            Storage::put('/public/charts/AIRAC_'.$airac.'/'.$dttp_c.'.zip', $res_c->getBody());
            \Zipper::make(base_path('storage/app/public/charts/AIRAC_'.$airac.'/'.$dttp_c.'.zip'))->extractTo(base_path('public/charts/AIRAC_'.$airac));
            Storage::delete('/public/storage/temporary_storage/'.$storage.$dttp_c);

            $res_d = $client->request('GET', 'https://aeronav.faa.gov/upload_313-d/terminal/'.$dttp_d.'.zip', ['sink' => $storage.$dttp_d]);
            Storage::put('/public/charts/AIRAC_'.$airac.'/'.$dttp_d.'.zip', $res_d->getBody());
            \Zipper::make(base_path('storage/app/public/charts/AIRAC_'.$airac.'/'.$dttp_d.'.zip'))->extractTo(base_path('public/charts/AIRAC_'.$airac));
            Storage::delete('/public/storage/temporary_storage/'.$storage.$dttp_d);

            $res_e = $client->request('GET', 'https://aeronav.faa.gov/upload_313-d/terminal/'.$dttp_e.'.zip', ['sink' => $storage.$dttp_e]);
            Storage::put('/public/charts/AIRAC_'.$airac.'/'.$dttp_e.'.zip', $res_e->getBody());
            \Zipper::make(base_path('storage/app/public/charts/AIRAC_'.$airac.'/'.$dttp_e.'.zip'))->extractTo(base_path('public/charts/AIRAC_'.$airac.'/'.$dttp_e));
            Storage::delete('/public/storage/temporary_storage/'.$storage.$dttp_e);

            $next->updated = 1;
            $next->save();

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
