<?php

namespace App\Http\Controllers;

use App\VatController;
use App\VatPilot;
use Illuminate\Http\Request;

class VATSIMController extends Controller
{
    public function searchPilotsByAirport(Request $request) {
        $apt = strtoupper($request->apt);
        $apts = explode(',', $apt);
        $data = array();
        if($apts != null) {
            foreach($apts as $a) {
                if(strlen($a) < 4) {
                    $a = 'K'.$a;
                }
                $dep = VatPilot::where('departure', $a)->get()->toArray();
                $arr = VatPilot::where('arrival', $a)->get()->toArray();
                $data[$a] = ['Departures' => $dep, 'Arrivals' => $arr];
            }

            return response()->json($data);
        } else {
            return response()->json(['status' => 'error', 'status_code' => '404', 'message' => 'Please specify an airport.'], 404);
        }
    }

    public function searchControllersByFacility(Request $request) {
        $fac = strtoupper($request->facility);
        $facs = explode(',', $fac);
        $data = array();
        if($facs != null) {
            foreach($facs as $a) {
                if(strlen($a) < 4) {
                    $a = 'K'.$a;
                }
                $controllers = VatController::where('callsign', 'LIKE', $a.'%')->get()->toArray();
                $data[$a] = $controllers;
            }

            return response()->json($data);
        } else {
            return response()->json(['status' => 'error', 'status_code' => '404', 'message' => 'Please specify an airport.'], 404);
        }
    }
}
