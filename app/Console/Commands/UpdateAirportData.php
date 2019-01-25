<?php

namespace App\Console\Commands;

use App\AirportData;
use DB;
use GuzzleHttp\Client;
use Storage;

use Illuminate\Console\Command;

class UpdateAirportData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AirportData:Update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the airport data in the database';

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
        $res = $client->request('GET', 'https://www.faa.gov/airports/airport_safety/airportdata_5010/menu/nfdcfacilitiesexport.cfm?Region=&District=&State=&County=&City=&Use=&Certification=', ['sink' => base_path('public/storage/NfdcFacilities.xls')]);

        DB::table('airport_data')->truncate();

        $result = array();
        $fp = fopen(base_path('public/storage/NfdcFacilities.xls'),'r');
            while (($line = fgetcsv($fp, 0, "\t")) !== FALSE) {
                if ($line) {
                    $result[] = $line;
                }
            }
        fclose($fp);

        foreach($result as $r) {
            if($r[0] != "SiteNumber") {
                $apt_faa = substr($r[2], 1);
                $apt_icao = $r[101];
                if(strlen($apt_icao) < 4 && strlen($apt_faa) < 4) {
                    $apt_icao = 'K'.$apt_faa;
                } elseif(strlen($apt_icao) < 4 && strlen($apt_faa) == 4) {
                    $apt_icao = $apt_faa;
                }

                $apt = new AirportData;
                $apt->site_number = $r[0];
                $apt->type = $r[1];
                $apt->facility_name = $r[11];
                $apt->faa_ident = $apt_faa;
                $apt->icao_ident = $apt_icao;
                $apt->region = $r[4];
                $apt->district_office = $r[5];
                $apt->state = $r[6];
                $apt->state_full = $r[7];
                $apt->county = $r[8];
                $apt->city = $r[10];
                $apt->ownership = $r[12];
                $apt->use = $r[13];
                $apt->manager = $r[18];
                $apt->manager_phone = $r[21];
                $apt->latitude = $r[22];
                $apt->latitude_sec = $r[23];
                $apt->longitude = $r[24];
                $apt->longitude_sec = $r[25];
                $apt->elevation = $r[27];
                $apt->magnetic_variation = $r[29];
                $apt->tpa = $r[31];
                $apt->vfr_sectional = $r[32];
                $apt->boundary_artcc = $r[36];
                $apt->boundary_artcc_name = $r[38];
                $apt->responsible_artcc = $r[39];
                $apt->responsible_artcc_name = $r[41];
                $apt->fss_phone_number = $r[45];
                $apt->fss_phone_numer_tollfree = $r[46];
                $apt->notam_facility_ident = $r[50];
                $apt->status = $r[53];
                $apt->certification_typedate = $r[54];
                $apt->customs_airport_of_entry = $r[57];
                $apt->military_joint_use = $r[59];
                $apt->military_landing = $r[60];
                $apt->lighting_schedule = $r[70];
                $apt->beacon_schedule = $r[71];
                $apt->control_tower = $r[72];
                $apt->unicom = $r[73];
                $apt->ctaf = $r[74];
                $apt->effective_date = $r[3];
                $apt->save();
            }
        }
        Storage::delete('/public/NfdcFacilities.xls');
    }
}
