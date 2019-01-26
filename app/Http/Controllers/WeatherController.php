<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use SimpleXMLElement;

class WeatherController extends Controller
{
    public function searchMetar(Request $request) {
        $apts = $request->apt;
        if($apts == null) {
            return response()->json(['status' => 'error', 'status_code' => '404', 'message' => 'You must search for at least one airport.'], 404);
        }
        $client = new Client;
        $res = $client->request('GET', 'https://www.aviationweather.gov/adds/dataserver_current/httpparam?dataSource=metars&requestType=retrieve&format=xml&hoursBeforeNow=2&mostRecentForEachStation=true&stationString='.$apts);
        $res_data = new SimpleXMLElement($res->getBody());
        $results = $res_data->data['num_results']->__toString();

        if($results > 0) {
            $data = array();
            foreach($res_data->data->children()->METAR as $metar_data) {
                $raw = $metar_data->raw_text->__toString();
                $station = $metar_data->station_id->__toString();
                $obs_time = $metar_data->observation_time->__toString();
                $temp = $metar_data->temp_c->__toString();
                $dp = $metar_data->dewpoint_c->__toString();
                $wind_dir = $metar_data->wind_dir_degrees->__toString();
                $wind_spd = $metar_data->wind_speed_kt->__toString();
                $vis = $metar_data->visibility_statute_mi->__toString();
                $alt_hg = substr($metar_data->altim_in_hg, 0, 5);
                $alt_mb = $metar_data->sea_level_pressure_mb->__toString();
                if($metar_data->wx_string) {
                    $wx = $metar_data->wx_string->__toString();
                } else {
                    $wx = null;
                }
                $auto = strtolower($metar_data->quality_control_flags->auto_station->__toString());
                $sky_conds = $metar_data->sky_condition;
                $sky_conds_return = array();
                if(count($sky_conds) > 0) {
                    $i = 0;
                    foreach($sky_conds as $s) {
                        if($s['sky_cover']->__toString() != 'CLR') {
                            $sky_conds_return[$i] = array('coverage' => $s['sky_cover']->__toString(), 'base_agl' => $s['cloud_base_ft_agl']->__toString());
                        } else {
                            $sky_conds_return[0] = array('coverage' => 'CLR', 'base_agl' => null);
                        }
                        $i++;
                    }
                }
                $category = $metar_data->flight_category->__toString();
                $report_type = $metar_data->metar_type->__toString();

                $apt_data = [
                    'station_id' => $station,
                    'raw' => $raw,
                    'temp' => $temp,
                    'dewpoint' => $dp,
                    'wind' => $wind_dir,
                    'wind_vel' => $wind_spd,
                    'visibility' => $vis,
                    'alt_hg' => $alt_hg,
                    'alt_mb' => $alt_mb,
                    'wx' => $wx,
                    'auto_report' => $auto,
                    'sky_conditions' => $sky_conds_return,
                    'category' => $category,
                    'report_type' => $report_type,
                    'time_of_obs' => $obs_time
                ];

                $data[$station] = $apt_data;
            }
            return response()->json($data);
        } else {
            return response()->json(['status' => 'error', 'status_code' => '404', 'message' => 'No METAR found for that airport.'], 404);
        }
    }

    public function searchTaf(Request $request) {
        $apts = $request->apt;
        if($apts == null) {
            return response()->json(['status' => 'error', 'status_code' => '404', 'message' => 'You must search for at least one airport.'], 404);
        }
        $client = new Client;
        $res = $client->request('GET', 'https://www.aviationweather.gov/adds/dataserver_current/httpparam?dataSource=tafs&requestType=retrieve&format=xml&hoursBeforeNow=2&mostRecentForEachStation=true&stationString='.$apts);
        $res_data = new SimpleXMLElement($res->getBody());
        $results = $res_data->data['num_results']->__toString();

        if($results > 0) {
            $data = array();
            $j = 0;
            foreach($res_data->data->children()->TAF as $taf_data) {
                $raw = $taf_data->raw_text->__toString();
                $station = $taf_data->station_id->__toString();
                $issue_time = $taf_data->issue_time->__toString();
                $valid_time = $taf_data->valid_time_from->__toString().' - '.$taf_data->valid_time_to->__toString();
                $taf_lines = preg_split('@(?=FM|BECMG|TEMPO)@', $raw);
                $line_by_line = array();
                $i = 0;
                foreach($taf_lines as $t) {
                    $line_by_line[$i] = rtrim($t);
                    $i++;
                }

                $apt_data = [
                    'station_id' => $station,
                    'raw' => $raw,
                    'issue_time' => $issue_time,
                    'valid_time' => $valid_time,
                    'line_by_line' => $line_by_line
                ];

                $data[$station] = $apt_data;
                $j++;
            }
            return response()->json($data);
        } else {
            return response()->json(['status' => 'error', 'status_code' => '404', 'message' => 'No METAR found for that airport.'], 404);
        }
    }
}
