<?php

namespace App\Http\Controllers;

use App\CurrentAFD;
use App\CurrentChart;
use App\CurrentChangeChart;
use Illuminate\Http\Request;

class ChartsController extends Controller
{
    /**
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse|string
     *
     * @SWG\Get(
     *     path="/charts",
     *     summary="Get charts for a specified airport",
     *     description="Search for charts by ICAO or FAA identifier with optional grouping",
     *     produces={"application/json"},
     *     tags={"charts"},
     *     @SWG\Parameter(name="apt", in="query", description="FAA or ICAO airport identifier (KAVL or AVL). Separate multiple entries with a comma", required=true, type="string"),
     *     @SWG\Parameter(name="group", in="query", description="Optional grouping of the charts. 1 -> General, Departures, Arrivals, Approaches; 2 -> Airport Diagram only; 3 -> General only; 4 -> Departures only; 5 -> Arrivals only; 6 -> Approaches only; 7 -> Everything but General", required=false, type="integer"),
     *     @SWG\Response(
     *         response="403",
     *         description="Invalid group code",
     *         @SWG\Schema(ref="#/definitions/error"),
     *         examples={"application/json":{"status":"error","status_code":"403","message":"That is not a valid grouping code."}}
     *     ),
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
     *             @SWG\Property(property="id", type="integer", example="12982"),
     *             @SWG\Property(property="state", description="Two letter abbreviation of the state the facility resides in", type="string", example="NC"),
     *             @SWG\Property(property="state_full", description="Full name of the state the facility resides in", type="string", example="NORTH CAROLINA"),
     *             @SWG\Property(property="city", description="Name of the city the facility resides in", type="string", example="ASHEVILLE"),
     *             @SWG\Property(property="airport_name", description="Name of the facility", type="string", example="ASHEVILLE RGNL"),
     *             @SWG\Property(property="military", description="Military chart, Y -> Yes, N -> No", type="string", example="N"),
     *             @SWG\Property(property="faa_ident", description="Facility FAA identifier", type="string", example="AVL"),
     *             @SWG\Property(property="icao_ident", description="Facility ICAO identifier", type="string", example="KAVL"),
     *             @SWG\Property(property="chart_seq", description="Chart sequence number", type="string", example="10100"),
     *             @SWG\Property(property="chart_code", description="Code for the chart type", type="string", example="MIN"),
     *             @SWG\Property(property="chart_name", description="Name of the chart", type="string", example="TAKEOFF MINIMUMS"),
     *             @SWG\Property(property="pdf_name", description="Name of the chart PDF", type="string", example="SE2TO.PDF"),
     *             @SWG\Property(property="pdf_path", description="URL to the chart PDF", type="string", example="https://charts.aviationapi.com/AIRAC_190103/SE2TO.PDF"),
     *             @SWG\Property(property="created_at", type="timestamp", example="2019-01-26 21:18:00"),
     *             @SWG\Property(property="updated_at", type="timestamp", example="2019-01-26 21:18:00")
     *         )
     *     )
     * )
     */
    public function returnCharts(Request $request) {
        $airports = strtoupper($request->apt);
        $airport_array = explode(',', $airports);
        $group = $request->group;
        if($airports != null) {
            foreach($airport_array as $a) {
                if(strlen($a) == 3) {
                    $a = 'K'.$a;
                }
                $all_charts0 = CurrentChart::where('icao_ident', $a);
                $all_charts1 = CurrentChart::where('icao_ident', $a);
                $all_charts2 = CurrentChart::where('icao_ident', $a);
                $all_charts3 = CurrentChart::where('icao_ident', $a);

                if(isset($group)) {
                    if($group == 1) {
                        //Groups by type
                        $general = $all_charts0->where(function($c) {
                            $c->where('chart_code', 'APD')
                              ->orWhere('chart_code', 'MIN')
                              ->orWhere('chart_code', 'LAH')
                              ->orWhere('chart_code', 'HOT');
                        })->orderBy('chart_code', 'ASC')->get()->toArray();
                        $dp = $all_charts1->where('chart_code', 'DP')->get()->toArray();
                        $star = $all_charts2->where('chart_code', 'STAR')->get()->toArray();
                        $capp = $all_charts3->where('chart_code', 'IAP')->orWhere('chart_code', 'CVFP')->orderBy('chart_code', 'ASC')->get()->toArray();
                        $data_a = [
                            "General" => $general,
                            "DP" => $dp,
                            "STAR" => $star,
                            "CAPP" => $capp
                        ];
                    } elseif($group == 2) {
                        //APD only
                        $data_a = $all_charts0->where('chart_code', 'APD')->get()->toArray();
                    } elseif($group == 3) {
                        //General only
                        $data_a = $all_charts0->where(function($airport) {
                            $airport->where('chart_code', 'APD')
                                    ->orWhere('chart_code', 'MIN')
                                    ->orWhere('chart_code', 'LAH')
                                    ->orWhere('chart_code', 'HOT');
                        })->orderBy('chart_code', 'ASC')->get()->toArray();
                    } elseif($group == 4) {
                        //DP only
                        $data_a = $all_charts0->where('chart_code', 'DP')->get()->toArray();
                    } elseif($group == 5) {
                        //STAR only
                        $data_a = $all_charts0->where('chart_code', 'STAR')->get()->toArray();
                    } elseif($group == 6) {
                        //CAPP only
                        $data_a = $all_charts0->where('chart_code', 'IAP')->orWhere('chart_code', 'CVFP')->orderBy('chart_code', 'ASC')->get()->toArray();
                    } elseif($group == 7) {
                        //Groups by DP, STAR, CAPP only
                        $dp = $all_charts0->where('chart_code', 'DP')->get()->toArray();
                        $star = $all_charts1->where('chart_code', 'STAR')->get()->toArray();
                        $capp = $all_charts2->where('chart_code', 'IAP')->orWhere('chart_code', 'CVFP')->orderBy('chart_code', 'ASC')->get()->toArray();
                        $data_a = [
                            "DP" => $dp,
                            "STAR" => $star,
                            "CAPP" => $capp
                        ];
                    } else {
                        return response()->json(['status' => 'error', 'status_code' => '403', 'message' => 'That is not a valid grouping code.'], 403);
                    }
                } else {
                    $data_a = $all_charts0->get()->toArray();
                }
                $data[$a] = $data_a;
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
     *     path="/charts/changes",
     *     summary="Get chart changes by airport or chart name",
     *     description="Search for charts by ICAO or FAA identifier or chart name",
     *     produces={"application/json"},
     *     tags={"charts"},
     *     @SWG\Parameter(name="apt", in="query", description="FAA or ICAO airport identifier (KAVL or AVL)", required=true, type="string"),
     *     @SWG\Parameter(name="chart_name", in="query", description="Partial or full name of the chart/procedure", required=false, type="string"),
     *     @SWG\Response(
     *         response="404",
     *         description="No airport specified",
     *         @SWG\Schema(ref="#/definitions/error"),
     *         examples={"application/json":{"status":"error","status_code":"404","message":"Please specify either an airport or a procedure name."}}
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="OK",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(property="id", type="integer", example="541"),
     *             @SWG\Property(property="state", description="Two letter abbreviation of the state the facility resides in", type="string", example="NC"),
     *             @SWG\Property(property="state_full", description="Full name of the state the facility resides in", type="string", example="NORTH CAROLINA"),
     *             @SWG\Property(property="city", description="Name of the city the facility resides in", type="string", example="CHARLOTTE"),
     *             @SWG\Property(property="airport_name", description="Name of the facility", type="string", example="CHARLOTTE/DOUGLAS INTL"),
     *             @SWG\Property(property="military", description="Military chart, Y -> Yes, N -> No", type="string", example="N"),
     *             @SWG\Property(property="faa_ident", description="Facility FAA identifier", type="string", example="CLT"),
     *             @SWG\Property(property="icao_ident", description="Facility ICAO identifier", type="string", example="KCLT"),
     *             @SWG\Property(property="chart_seq", description="Chart sequence number", type="string", example="19003"),
     *             @SWG\Property(property="chart_code", description="Code for the chart type", type="string", example="DP"),
     *             @SWG\Property(property="chart_name", description="Name of the chart", type="string", example="WEAZL FOUR"),
     *             @SWG\Property(property="pdf_name", description="Name of the chart comparison PDF", type="string", example="00078WEAZL_CMP.PDF"),
     *             @SWG\Property(property="pdf_path", description="URL to the chart comparison PDF", type="string", example="https://charts.aviationapi.com/AIRAC_190103/DDTPPE_190103/compare_pdf/00078WEAZL_CMP.PDF"),
     *             @SWG\Property(property="created_at", type="timestamp", example="2019-01-26 21:18:00"),
     *             @SWG\Property(property="updated_at", type="timestamp", example="2019-01-26 21:18:00")
     *         )
     *     )
     * )
     */
    public function returnChartChanges(Request $request) {
        $airport_id = strtoupper($request->apt);
        $chart = strtoupper($request->chart_name);
        if($airport_id != null || $chart != null) {
            if($airport_id != null && $chart != null) {
                if(strlen($airport_id) == 3) {
                    $airport_id = 'K'.$airport_id;
                }
                $data = CurrentChangeChart::where('chart_name', 'LIKE', '%'.$chart.'%')->where('icao_ident', $airport_id)->get()->toArray();
            } elseif($chart != null) {
                $data = CurrentChangeChart::where('chart_name', 'LIKE', '%'.$chart.'%')->get()->toArray();
            } else {
                if(strlen($airport_id) == 3) {
                    $airport_id = 'K'.$airport_id;
                }
                $data = CurrentChangeChart::where('icao_ident', $airport_id)->get()->toArray();
            }
            return response()->json($data);
        } else {
            return response()->json(['status' => 'error', 'status_code' => '404', 'message' => 'Please specify either an airport or a procedure name.'], 404);
        }
    }

    /**
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse|string
     *
     * @SWG\Get(
     *     path="/charts/afd",
     *     summary="Get the AFD for a specified airport",
     *     description="Search for AFD by ICAO or FAA identifier",
     *     produces={"application/json"},
     *     tags={"charts"},
     *     @SWG\Parameter(name="apt", in="query", description="FAA or ICAO airport identifier (KAVL or AVL)", required=true, type="string"),
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
     *             @SWG\Property(property="id", type="integer", example="541"),
     *             @SWG\Property(property="state", description="Full name of the state the facility resides in", type="string", example="NORTH CAROLINA"),
     *             @SWG\Property(property="city", description="Name of the city the facility resides in", type="string", example="CHARLOTTE"),
     *             @SWG\Property(property="airport_name", description="Name of the facility", type="string", example="CHARLOTTE/DOUGLAS INTL"),
     *             @SWG\Property(property="icao_ident", description="Facility ICAO identifier", type="string", example="KCLT"),
     *             @SWG\Property(property="pdf_name", description="Name of the chart comparison PDF", type="string", example="SE_269_03JAN2019.PDF"),
     *             @SWG\Property(property="pdf_path", description="URL to the chart comparison PDF", type="string", example="https://charts.aviationapi.com/AFD/AIRAC_190103/2_single_page_PDFs/SE_269_03JAN2019.PDF"),
     *             @SWG\Property(property="created_at", type="timestamp", example="2019-01-26 21:18:00"),
     *             @SWG\Property(property="updated_at", type="timestamp", example="2019-01-26 21:18:00")
     *         )
     *     )
     * )
     */
    public function returnAFD(Request $request) {
        $airport_id = strtoupper($request->apt);
        if($airport_id != null) {
            if(strlen($airport_id) == 3) {
                $airport_id = 'K'.$airport_id;
            }
            $data = CurrentAFD::where('icao_ident', $airport_id)->get()->toArray();

            return response()->json($data);
        } else {
            return response()->json(['status' => 'error', 'status_code' => '404', 'message' => 'Please specify an airport.'], 404);
        }
    }
}
