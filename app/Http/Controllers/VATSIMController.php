<?php

namespace App\Http\Controllers;

use App\VatController;
use App\VatPilot;
use Illuminate\Http\Request;

class VATSIMController extends Controller
{
    /**
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse|string
     *
     * @SWG\Get(
     *     path="/vatsim/pilots",
     *     summary="Get all the arrivals and/or departures into an airport on VATSIM",
     *     description="Search for all the arrivals and/or departures into an airport on VATSIM",
     *     produces={"application/json"},
     *     tags={"VATSIM"},
     *     @SWG\Parameter(name="apt", in="query", description="FAA or ICAO airport identifier (KATL or ATL). Separate multiple entries with a comma", required=true, type="string"),
     *     @SWG\Parameter(name="dep", in="query", description="Show only departures? 1 -> true, 2 -> false", required=false, type="integer"),
     *     @SWG\Parameter(name="arr", in="query", description="Show only arrivals? 1 -> true, 2 -> false", required=false, type="integer"),
     *     @SWG\Response(
     *         response="404",
     *         description="No airport specified",
     *         @SWG\Schema(ref="#/definitions/error"),
     *         examples={"application/json":{"status":"error","status_code":"404","message":"Please specify an airport."}}
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="OK",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(property="id", type="integer", example="5"),
     *             @SWG\Property(property="callsign", description="Callsign of the aircraft", type="string", example="AAL1567"),
     *             @SWG\Property(property="cid", description="CID of the pilot", type="integer", example="921828"),
     *             @SWG\Property(property="name", description="Name of the pilot and home base (if applicable)", type="string", example="Carl Stanbridge EGLL"),
     *             @SWG\Property(property="latitude", description="Latitude of the aircraft", type="string", example="32.30375"),
     *             @SWG\Property(property="longitutde", description="Longitutde of the aircraft", type="string", example="-94.69470"),
     *             @SWG\Property(property="altitude", description="Reported aircraft altitude, in feet", type="string", example="35974"),
     *             @SWG\Property(property="ground_speed", description="Reported aircraft ground speed, in knots", type="string", example="403"),
     *             @SWG\Property(property="heading", description="Reported aircraft heading", type="integer", example="265"),
     *             @SWG\Property(property="transponder", description="Aircraft's transponder code", type="integer", example="2011"),
     *             @SWG\Property(property="aircraft", description="Aircraft's aircraft type and equipment code (if applicable)", type="string", example="B738/L"),
     *             @SWG\Property(property="departure", description="Departure airport identifier", type="string", example="KATL"),
     *             @SWG\Property(property="nm_from_dep", description="Distance from departure airport, in NM", type="string", example="630.48"),
     *             @SWG\Property(property="arrival", description="Arrival airport identifier", type="string", example="KDFW"),
     *             @SWG\Property(property="nm_from_arr", description="Distance from arrival airport, in NM", type="string", example="14.39"),
     *             @SWG\Property(property="alternate", description="Alternate airport identifier, if applicable", type="string", example="KIAH"),
     *             @SWG\Property(property="route", description="Aircraft's route of flight", type="string", example="KAJIN2 STNGA MLU YUYUN BEREE1"),
     *             @SWG\Property(property="flight_rules", description="Flight rules, VFR -> Visual Flight Rules, IFR -> Instrument Flight Rules", type="string", example="IFR"),
     *             @SWG\Property(property="filed_altitude", description="Planned altitude of flight", type="string", example="36000"),
     *             @SWG\Property(property="filed_tas", description="Filed cruise true airspeed", type="string", example="465"),
     *             @SWG\Property(property="filed_time_enroute", description="Estimated filed time enroute", type="string", example="1:56"),
     *             @SWG\Property(property="filed_fuel_onboard", description="Estimated filed maximum cruise time for fuel onboard", type="string", example="3:30"),
     *             @SWG\Property(property="remarks", description="Flightplan remarks", type="string", example="+VFPS+/V/PBN/A1B1C1D1S1S2 DOF/190128 REG/N806SB EET/KZME0030 KZFW0059 OPR/AAL PER/C RMK/TCAS SIMBRIEF"),
     *             @SWG\Property(property="stage_of_flight", description="Estimated current stage of flight", type="string", example="Descent"),
     *             @SWG\Property(property="created_at", type="timestamp", example="2019-01-26 21:18:00"),
     *             @SWG\Property(property="updated_at", type="timestamp", example="2019-01-26 21:18:00")
     *         )
     *     )
     * )
     */
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

    /**
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse|string
     *
     * @SWG\Get(
     *     path="/vatsim/controllers",
     *     summary="Get all the controllers at a specified facility on VATSIM",
     *     description="Search for all the at a specified facility on VATSIM",
     *     produces={"application/json"},
     *     tags={"VATSIM"},
     *     @SWG\Parameter(name="fac", in="query", description="Airport facility identifier. This should be what is used in the callsign on VATSIM (CLT). Separate multiple entries with a comma", required=true, type="string"),
     *     @SWG\Response(
     *         response="404",
     *         description="No facility specified",
     *         @SWG\Schema(ref="#/definitions/error"),
     *         examples={"application/json":{"status":"error","status_code":"404","message":"Please specify a facility."}}
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="OK",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(property="id", type="integer", example="4"),
     *             @SWG\Property(property="callsign", description="Controller callsign", type="string", example="CHI_35_CTR"),
     *             @SWG\Property(property="cid", description="CID of the controller", type="integer", example="991063"),
     *             @SWG\Property(property="name", description="Name of the controller", type="string", example="Darryl Flora"),
     *             @SWG\Property(property="frequency", description="Frequency the controller is transmitting and receiving on", type="string", example="134.870"),
     *             @SWG\Property(property="atis", description="Controller ATIS", type="string", example="$ rw.liveatc.net/ZAU_134.870"),
     *             @SWG\Property(property="time_logon", description="Controller logon date and time", type="timestamp", example="2019-01-28 14:14:04"),
     *             @SWG\Property(property="time_online", description="Amount of time the controller has been logged on in hours, minutes, and seconds", type="string", example="01:04:24"),
     *             @SWG\Property(property="created_at", type="timestamp", example="2019-01-26 21:18:00"),
     *             @SWG\Property(property="updated_at", type="timestamp", example="2019-01-26 21:18:00")
     *         )
     *     )
     * )
     */
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
