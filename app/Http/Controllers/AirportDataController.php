<?php

namespace App\Http\Controllers;

use App\AirportData;
use Illuminate\Http\Request;

class AirportDataController extends Controller
{
    public function getAllAirports() {
        $data = AirportData::get()->toArray();
        return response()->json($data);
    }

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
