<?php

namespace App\Http\Controllers;

use App\AirportData;
use Illuminate\Http\Request;

class AirportDataController extends Controller
{

    /**
    *   API response for returning all of the airport information in the database
    *
    *   path = /v1/airports
    *   summary = "Gets all of the airports in the database"
    *   description = "Gets all of the airports in the database"
    *   produces = {"application/json"}
    *   tags = {"airports", "airports information"}
    *   examples = {
    *       "application/json":[
    *             {"id":10159,"site_number":"16517.5*A","type":"AIRPORT","facility_name":"ASHEVILLE RGNL","faa_ident":"AVL","icao_ident":"KAVL","region":"ASO","district_office":"MEM","state":"NC","state_full":"NORTH CAROLINA","county":"BUNCOMBE","city":"ASHEVILLE","ownership":"PU","use":"PU","manager":"LEW S. BLEIWEIS, A.A.E.","manager_phone":"  (828) 684-2226","latitude":"35-26-04.0000N","latitude_sec":"127564.0000N","longitude":"082-32
    *             33.8240W","longitude_sec":"297153.8240W","elevation":"2162","magnetic_variation":"07W","tpa":"","vfr_sectional":"ATLANTA","boundary_artcc":"ZTL","boundary_artcc_name":"ATLANTA","responsible_artcc":"","responsible_artcc_name":"","fss_phone_number":"","fss_phone_numer_tollfree":"1-800-WX-BRIEF","notam_facility_ident":"AVL","status":"O","certification_typedate":"I B S 05\/1973","customs_airport_of_entry":"N","military_joint_use":"N","military_landing":"Y","lighting_schedule":"SEE
    *             RMK","beacon_schedule":"SS-SR","control_tower":"Y","unicom":"122.950","ctaf":"121.100","effective_date":"01\/03\/2019","created_at":"2019-01-21 05:22:07","updated_at":"2019-01-21 05:22:07"}
    *       ]
    *   }
    *
    **/
    public function getAllAirports() {
        $data = AirportData::get()->toArray();
        return response()->json($data);
    }

    /**
    *   API response for searching the airport databse by airport or bordering/responsible ARTCC
    *
    *   path = /v1/airports/search
    *   summary = "Searches for airports in the database"
    *   description = "Searches for airports in the database"
    *   produces = {"application/json"}
    *   tags = {"airports", "airports information", "airports search"}
    *   examples = {
    *       "application/json":[
    *             {"id":10159,"site_number":"16517.5*A","type":"AIRPORT","facility_name":"ASHEVILLE RGNL","faa_ident":"AVL","icao_ident":"KAVL","region":"ASO","district_office":"MEM","state":"NC","state_full":"NORTH CAROLINA","county":"BUNCOMBE","city":"ASHEVILLE","ownership":"PU","use":"PU","manager":"LEW S. BLEIWEIS, A.A.E.","manager_phone":"  (828) 684-2226","latitude":"35-26-04.0000N","latitude_sec":"127564.0000N","longitude":"082-32
    *             33.8240W","longitude_sec":"297153.8240W","elevation":"2162","magnetic_variation":"07W","tpa":"","vfr_sectional":"ATLANTA","boundary_artcc":"ZTL","boundary_artcc_name":"ATLANTA","responsible_artcc":"","responsible_artcc_name":"","fss_phone_number":"","fss_phone_numer_tollfree":"1-800-WX-BRIEF","notam_facility_ident":"AVL","status":"O","certification_typedate":"I B S 05\/1973","customs_airport_of_entry":"N","military_joint_use":"N","military_landing":"Y","lighting_schedule":"SEE
    *             RMK","beacon_schedule":"SS-SR","control_tower":"Y","unicom":"122.950","ctaf":"121.100","effective_date":"01\/03\/2019","created_at":"2019-01-21 05:22:07","updated_at":"2019-01-21 05:22:07"}
    *       ]
    *   }
    *
    **/
    public function searchByAirportName(Request $request) {
        $apt= $request->apt;
        $b_artcc = $request->boundary;
        $r_artcc = $request->responsible;
        if(isset($apt) && isset($b_artcc) && isset($r_artcc)) {
            return response()->json(['status' => 'error', 'status_code' => '500', 'message' => 'You must search by only one parameter'], 500);
        } elseif(isset($apt) && isset($b_artcc)) {
            return response()->json(['status' => 'error', 'status_code' => '500', 'message' => 'You must search by only one parameter'], 500);
        } elseif(isset($b_artcc) && isset($r_artcc)) {
            return response()->json(['status' => 'error', 'status_code' => '500', 'message' => 'You must search by only one parameter'], 500);
        } elseif(isset($apt) && isset($r_artcc)) {
            return response()->json(['status' => 'error', 'status_code' => '500', 'message' => 'You must search by only one parameter'], 500);
        }

        if(isset($apt)) {
            $apt = strtoupper($apt);
            if(strlen($apt) < 4) {
                $data = AirportData::where('faa_ident', $apt)->get()->toArray();
            } else {
                $data = AirportData::where('icao_ident', $apt)->get()->toArray();
            }
        } elseif(isset($b_artcc)) {
            $b_artcc = strtoupper($b_artcc);
            if(strlen($b_artcc) == 3) {
                $data = AirportData::where('boundary_artcc', $b_artcc)->get()->toArray();
            } else {
                $data = AirportData::where('boundary_artcc_name', $b_artcc)->get()->toArray();
            }
        } elseif(isset($r_artcc)) {
            $r_artcc = strtoupper($r_artcc);
            if(strlen($r_artcc) == 3) {
                $ids = AirportData::where('responsible_artcc', $r_artcc)->orWhere('boundary_artcc', $r_artcc)->get()->filter(function($apt) use($r_artcc) {
                    if(strlen($apt->responsible_artcc) == 0) {
                       return $apt;
                    } elseif(strlen($apt->boundary_artcc) != 0 && strlen($apt->responsible_artcc) != 0) {
                        return $apt->responsible_artcc == $r_artcc;
                    }
                })->pluck('id')->toArray();

                $data = AirportData::find($ids)->toArray();
            } else {
                $ids = AirportData::where('responsible_artcc_name', $r_artcc)->orWhere('boundary_artcc_name', $r_artcc)->get()->filter(function($apt) use($r_artcc) {
                    if(strlen($apt->responsible_artcc_name) == 0) {
                       return $apt;
                   } elseif(strlen($apt->boundary_artcc_name) != 0 && strlen($apt->responsible_artcc_name) != 0) {
                        return $apt->responsible_artcc_name == $r_artcc;
                    }
                })->pluck('id')->toArray();

                $data = AirportData::find($ids)->toArray();
            }
        } else {
            return response()->json(['status' => 'error', 'status_code' => '500', 'message' => 'You must search by at least one parameter'], 500);
        }

        if(count($data) < 1) {
            return response()->json(null);
        }

        return response()->json($data);
    }
}
