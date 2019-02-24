<?php

namespace App\Console\Commands;

use App\AirportCord;
use App\VatController;
use App\VatPilot;
use Carbon\Carbon;
use Facades\App\Repository\Vatsim;
use DB;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class GetVatConnections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Update:VatConnections';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets all VATSIM connections every two minutes';

    //Calculates distance between two coordinated in nautical miles
    //Thanks to Jeffrey Sydenham
    protected function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $lat1 = $lat1 / 180 * pi();
    	$lon1 = $lon1 / 180 * pi();
    	$lat2 = $lat2 / 180 * pi();
    	$lon2 = $lon2 / 180 * pi();
    	$dlon = $lon2 - $lon1;
    	$dlat = $lat2 - $lat1;
    	$a = sin($dlat/2) * sin($dlat/2) + cos($lat1) * cos($lat2) * sin($dlon/2) * sin($dlon/2);

    	$c = 2 * atan2(sqrt($a), sqrt(1-$a));

    	$R = 6366.707; // was 6378.137;

    	$dist = $R * $c * .54;
        return round($dist, 2);
    }

    //Calculates a pilot's stage of flight
    protected function stageOfFlight($gs, $altitude, $filed_altitude, $nm_from_dep, $nm_from_arr) {
        $cruise_top = (int)$filed_altitude + 499;
        $cruise_bottom = (int)$filed_altitude - 499;
        if($gs == 0) {
            $stage = 'Parked';
        } elseif($gs < 40 && $gs != 0 && $nm_from_dep <= 2) {
            $stage = 'Taxi for Departure';
        } elseif($altitude < $filed_altitude && $gs > 40 && $nm_from_dep < $nm_from_arr) {
            $stage = 'Climb';
        } elseif(($altitude < $cruise_top && $altitude > $cruise_bottom && $gs > 40) || ($nm_from_dep > 50 && $nm_from_arr > 50 && $gs > 40)) {
            $stage = 'Cruise';
        } elseif($altitude < $filed_altitude && $gs > 40 && $nm_from_dep > $nm_from_arr) {
            $stage = 'Descent';
        } elseif($altitude < $filed_altitude && $gs > 40 && $nm_from_arr <= 10) {
            $stage = 'Landing';
        } elseif($gs < 40 && $gs != 0 && $nm_from_arr <= 2) {
            $stage = 'Taxi to Parking';
        } else {
            $stage = 'Unknown';
        }

        return $stage;
    }

    //Determines a pilot's flight rules
    protected function flightRules($flight_rules) {
        if($flight_rules == 'I') {
            $flight_rules = 'IFR';
        } elseif($flight_rules == 'V') {
            $flight_rules = 'VFR';
        } else {
            $flight_rules = 'Unknown';
        }

        return $flight_rules;
    }

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
        $lucky_number = rand();
        if($lucky_number % 2 == 0) {
            $clients_url = 'http://vatsim-data.hardern.net/vatsim-data.txt';
        } else {
            $clients_url = 'http://info.vroute.net/vatsim-data.txt';
        }
        $client = new Client;
        $res = $client->request('GET', $clients_url);

        $lines = explode("\n", $res->getBody());
        DB::table('vatsim_pilots')->truncate();
        DB::table('vatsim_controllers')->truncate();
        $clients_sec = 0;
        foreach($lines as $line) {
            if(substr($line, -1) == "\r") {
                $line = substr($line, 0, -1);
            }
            if($line == '!CLIENTS:') {
                $clients_sec = 1;
            } elseif($line == ';') {
                $clients_sec = 0;
            }
            if($clients_sec == 1 && $line != '!CLIENTS:') {
                $parts = explode(':', $line);
                $line = list($callsign, $cid, $realname, $clienttype, $frequency, $latitude, $longitude, $altitude, $groundspeed, $planned_aircraft, $planned_tascruise, $planned_depairport, $planned_altitude, $planned_destairport, $server, $protrevision, $rating, $transponder, $facilitytype, $visualrange, $planned_revision, $planned_flighttype, $planned_deptime, $planned_actdeptime, $planned_hrsenroute, $planned_minenroute, $planned_hrsfuel, $planned_minfuel, $planned_altairport, $planned_remarks, $planned_route, $planned_depairport_lat, $planned_depairport_lon, $planned_destairport_lat, $planned_destairport_lon, $atis_message, $time_last_atis_received, $time_logon, $heading, $QNH_iHg, $QNH_Mb) = explode(':', $line);
                if($clienttype == 'PILOT') {
                    $pilot = new VatPilot;
                    if($callsign != null) {
                        $pilot->callsign = $callsign;
                    }
                    if($cid != null) {
                        $pilot->cid = $cid;
                    }
                    if($realname != null) {
                        $pilot->name = $realname;
                    }
                    if($latitude != null) {
                        $pilot->latitude = $latitude;
                    }
                    if($longitude != null) {
                        $pilot->longitude = $longitude;
                    }
                    if($altitude != null) {
                        $pilot->altitude = $altitude;
                    }
                    if($groundspeed != null) {
                        $pilot->ground_speed = $groundspeed;
                    }
                    if($heading != null) {
                        $pilot->heading = $heading;
                    }
                    if($transponder != null) {
                        $pilot->transponder = $transponder;
                    }
                    if($planned_aircraft != null) {
                        $pilot->aircraft = $planned_aircraft;
                    }
                    if($planned_depairport != null) {
                        $pilot->departure = $planned_depairport;
                    }
                    if($planned_destairport != null) {
                        $pilot->arrival = $planned_destairport;
                    }
                    if($latitude != null && $longitude != null && $planned_depairport != null) {
                        $apt_cord = AirportCord::where('ident', $planned_depairport)->first();
                        if($apt_cord != null) {
                            $dep_lat = $apt_cord->latitude;
                            $dep_lon = $apt_cord->longitude;
                            $nm_from_dep = $this->calculateDistance($latitude, $longitude, $dep_lat, $dep_lon);
                        } else {
                            $nm_from_dep = null;
                        }
                    } else {
                        $nm_from_dep = null;
                    }
                    $pilot->nm_from_dep = $nm_from_dep;
                    if($latitude != null && $longitude != null && $planned_destairport != null) {
                        $apt_cord = AirportCord::where('ident', $planned_destairport)->first();
                        if($apt_cord != null) {
                            $dep_lat = $apt_cord->latitude;
                            $dep_lon = $apt_cord->longitude;
                            $nm_from_arr = $this->calculateDistance($latitude, $longitude, $dep_lat, $dep_lon);
                        } else {
                            $nm_from_arr = null;
                        }
                    } else {
                        $nm_from_arr = null;
                    }
                    $pilot->nm_from_arr = $nm_from_arr;
                    if($planned_altairport != null) {
                        $pilot->alternate = $planned_altairport;
                    }
                    if($planned_route != null) {
                        $pilot->route = $planned_route;
                    }
                    if($planned_altitude != null) {
                        $pilot->filed_altitude = $planned_altitude;
                    }
                    if($planned_tascruise != null) {
                        $pilot->filed_tas = $planned_tascruise;
                    }
                    if($planned_hrsenroute != null) {
                        $pilot->filed_time_enroute = $planned_hrsenroute.':'.$planned_minenroute;
                    }
                    if($planned_hrsfuel != null) {
                        $pilot->filed_fuel_onboard = $planned_hrsfuel.':'.$planned_minfuel;
                    }
                    if($planned_remarks != null) {
                        $pilot->remarks = $planned_remarks;
                    }
                    if($groundspeed != null && $altitude != null && $planned_altitude != null && $nm_from_dep != null && $nm_from_arr != null) {
                        $pilot->stage_of_flight = $this->stageOfFlight($groundspeed, $altitude, $planned_altitude, $nm_from_dep, $nm_from_arr);
                    } else {
                        $pilot->stage_of_flight = null;
                    }
                    if($planned_flighttype != null) {
                        $pilot->flight_rules = $this->flightRules($planned_flighttype);
                    } else {
                        $pilot->flight_rules = null;
                    }
                    if($time_logon != null) {
                        $pilot->time_logon = Carbon::create(substr($time_logon, 0, 4), substr($time_logon, 4, 2), substr($time_logon, 6, 2), substr($time_logon, 8, 2), substr($time_logon, 10, 2), substr($time_logon, 12, 2));;
                    }
                    $pilot->save();
                } elseif($clienttype == 'ATC') {
                    $controller = new VatController;
                    if($callsign != null) {
                        $controller->callsign = $callsign;
                    }
                    if($cid != null) {
                        $controller->cid = $cid;
                    }
                    if($realname != null) {
                        $controller->name = $realname;
                    }
                    if($frequency != null) {
                        $controller->frequency = $frequency;
                    }
                    if($atis_message != null) {
                        $controller->atis = utf8_encode($atis_message);
                    }
                    if($time_logon != null) {
                        $now = Carbon::now();
                        $time_logon_time = Carbon::create(substr($time_logon, 0, 4), substr($time_logon, 4, 2), substr($time_logon, 6, 2), substr($time_logon, 8, 2), substr($time_logon, 10, 2), substr($time_logon, 12, 2));
                        $diff_in_time = gmdate('H:i:s', $now->diffInSeconds($time_logon_time));
                        $controller->time_logon = $time_logon_time;
                        $controller->time_online = $diff_in_time;
                    }
                    $controller->save();
                }
            }
        }

        Vatsim::cacheAll();
    }
}
