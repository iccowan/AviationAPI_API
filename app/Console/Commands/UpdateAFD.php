<?php

namespace App\Console\Commands;

use App\NextAFD;
use Carbon\Carbon;
use Config;
use DB;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Chumper\Chumper\Zipper;
use SimpleXMLElement;
use Storage;

class UpdateAFD extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Update:AFD';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the AFD within 7 days of the cycle';

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
        $next = DB::table('afd_update_cycles')->where('updated', 0)->first();
        $next_cycle = Carbon::create('20'.$next->year, $next->month, $next->day);
        $time_until_next = $now->diffInDays($next_cycle);

        if($time_until_next != 7) {
            $airac = $next->year.$next->month.$next->day;
            $month = strtoupper($next_cycle->format('M'));

            $storage = base_path('/public/storage/charts/AFD/AIRAC_'.$airac.'/');
            Storage::makeDirectory('/public/charts/AFD/AIRAC_'.$airac);

            $client = new Client;
            $client->request('GET', 'https://aeronav.faa.gov/Upload_313-d/supplements/DCS_20'.$airac.'.zip', ['sink' => $storage.'DCS_20'.$airac.'.zip']);
            \Zipper::make(base_path('storage/app/public/charts/AFD/AIRAC_'.$airac.'/'.'DCS_20'.$airac.'.zip'))->extractTo(base_path('public/charts/AFD/AIRAC_'.$airac));

            $client = new Client;
            $base_pdf_path = Config::get('app.charts_url').'/AFD/AIRAC_'.$airac.'/2_single_page_PDFs/';
            $afd = $client->request('GET', Config::get('app.charts_url').'/AFD/AIRAC_'.$airac.'/1_xml/afd_'.$next->day.$month.'20'.$next->year.'.xml');
            $afd_db = new SimpleXMLElement($afd->getBody());
            DB::table('afd_next')->truncate();

            foreach($afd_db->location as $a) {
                $state = $a->attributes()->state->__toString();
                foreach($a->airport as $b) {
                    if($b->navidname == "") {
                        $afd = new NextAFD;
                        $afd->state = $state;
                        if(strlen($b->aptid->__toString()) < 4) {
                            $airport = 'K'.$b->aptid->__toString();
                        } else {
                            $airport = $b->aptid->__toString();
                        }
                        $afd->icao_ident = $airport;
                        $afd->airport_name = $b->aptname->__toString();
                        $afd->city = $b->aptcity->__toString();
                        $afd->pdf_name = $b->pdf->__toString();
                        $afd->pdf_path = $base_pdf_path.$b->pdf->__toString();
                        $afd->save();
                    }
                }
            }
            DB::table('afd_update_cycles')->where('id', $next->id)->update(['updated' => 1]);
        }
    }
}
