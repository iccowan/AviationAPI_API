<?php

namespace App\Http\Controllers;

use App\VatController;
use App\VatPilot;
use Illuminate\Http\Request;

class VATSIMController extends Controller
{
    public function searchPilots(Request $request) {
        $apt = strtoupper($request->apt);
        $dep = $request->dep;
        $arr = $request->arr;
        $apts = explode(',', $apt);
        $data = array();
        if($apt != null) {
            foreach($apts as $a) {
                if(strlen($a) < 4) {
                    $a = 'K'.$a;
                }
                if($dep == null && $arr == null) {
                    $dep = VatPilot::where('departure', $a)->get()->toArray();
                    $arr = VatPilot::where('arrival', $a)->get()->toArray();
                    $data[$a] = ['Departures' => $dep, 'Arrivals' => $arr];
                } elseif($dep == 1) {
                    $data[$a] = VatPilot::where('departure', $a)->get()->toArray();
                } elseif($arr == 1) {
                    $data[$a] = VatPilot::where('arrival', $a)->get()->toArray();
                } elseif($dep == 1 && $arr == 1) {
                    $dep = VatPilot::where('departure', $a)->get()->toArray();
                    $arr = VatPilot::where('arrival', $a)->get()->toArray();
                    $data[$a] = ['Departures' => $dep, 'Arrivals' => $arr];
                }
            }

            return response()->json($data);
        } else {
            return response()->json(['status' => 'error', 'status_code' => '404', 'message' => 'Please specify an airport.'], 404);
        }
    }

    public function searchControllers(Request $request) {
        $fac = strtoupper($request->fac);
        $facs = explode(',', $fac);
        $data = array();
        if($fac != null) {
            foreach($facs as $a) {
                $controllers = VatController::where('callsign', 'LIKE', $a.'%')->get()->toArray();
                $data[$a] = $controllers;
            }

            return response()->json($data);
        } else {
            return response()->json(['status' => 'error', 'status_code' => '404', 'message' => 'Please specify a facility.'], 404);
        }
    }
}
