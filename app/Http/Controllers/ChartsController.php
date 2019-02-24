<?php

namespace App\Http\Controllers;

use Facades\App\Repository\Charts;
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
     *             @SWG\Property(property="pdf_path", description="URL to the chart PDF", type="string", example="https://charts.aviationapi.com/AIRAC_190103/SE2TO.PDF")
     *         )
     *     )
     * )
     */
    public function returnCharts(Request $request) {
        $airports = strtoupper($request->apt);
        $airport_array = explode(',', $airports);
        $group = $request->group;
        if($airports != null && $airports != '%') {
            dd('hello world');
            foreach($airport_array as $a) {
                if(strlen($a) == 3) {
                    $a = 'K'.$a;
                }
                $all_charts0 = Charts::getByKey('CURRENTCHART')->where('icao_ident', $a);
                $all_charts1 = Charts::getByKey('CURRENTCHART')->where('icao_ident', $a);
                $all_charts2 = Charts::getByKey('CURRENTCHART')->where('icao_ident', $a);
                $all_charts3 = Charts::getByKey('CURRENTCHART')->where('icao_ident', $a);

                if(isset($group)) {
                    if($group == 1) {
                        //Groups by type
                        $general = $all_charts0->filter(function ($chart) {
                            return false != (stristr($chart->chart_code, 'APD') || stristr($chart->chart_code, 'MIN') || stristr($chart->chart_code, 'LAH') || stristr($chart->chart_code, 'HOT'));
                        })->sortBy(function ($chart) {
                            return $chart->chart_code;
                        })->toArray();
                        $general = Charts::removeId($general);
                        $dp = $all_charts1->where('chart_code', 'DP')->toArray();
                        $dp = Charts::removeId($dp);
                        $star = $all_charts2->where('chart_code', 'STAR')->toArray();
                        $star = Charts::removeId($star);
                        $capp = $all_charts3->filter(function ($chart) {
                            return false != (stristr($chart->chart_code, 'IAP') || stristr($chart->chart_code, 'CVFP') || stristr($chart->chart_code, 'IAP'));
                        })->sortBy(function ($chart) {
                            return $chart->chart_code;
                        })->toArray();
                        $capp = Charts::removeId($capp);
                        $data_a = [
                            "General" => $general,
                            "DP" => $dp,
                            "STAR" => $star,
                            "CAPP" => $capp
                        ];
                    } elseif($group == 2) {
                        //APD only
                        $data_a = $all_charts0->where('chart_code', 'APD')->toArray();
                        $data_a = Charts::removeId($data_a);
                    } elseif($group == 3) {
                        //General only
                        $data_a = $all_charts0->filter(function ($chart) {
                            return false != (stristr($chart->chart_code, 'APD') || stristr($chart->chart_code, 'MIN') || stristr($chart->chart_code, 'LAH') || stristr($chart->chart_code, 'HOT'));
                        })->sortBy(function ($chart) {
                            return $chart->chart_code;
                        })->toArray();
                        $data_a = Charts::removeId($data_a);
                    } elseif($group == 4) {
                        //DP only
                        $data_a = $all_charts0->where('chart_code', 'DP')->toArray();
                        $data_a = Charts::removeId($data_a);
                    } elseif($group == 5) {
                        //STAR only
                        $data_a = $all_charts0->where('chart_code', 'STAR')->toArray();
                        $data_a = Charts::removeId($data_a);
                    } elseif($group == 6) {
                        //CAPP only
                        $data_a = $all_charts0->filter(function ($chart) {
                            return false != (stristr($chart->chart_code, 'IAP') || stristr($chart->chart_code, 'CVFP') || stristr($chart->chart_code, 'IAP'));
                        })->sortBy(function ($chart) {
                            return $chart->chart_code;
                        })->toArray();
                        $data_a = Charts::removeId($data_a);
                    } elseif($group == 7) {
                        //Groups by DP, STAR, CAPP only
                        $dp = $all_charts0->where('chart_code', 'DP')->toArray();
                        $dp = Charts::removeId($dp);
                        $star = $all_charts1->where('chart_code', 'STAR')->toArray();
                        $star = Charts::removeId($star);
                        $capp = $all_charts2->filter(function ($chart) {
                            return false != (stristr($chart->chart_code, 'IAP') || stristr($chart->chart_code, 'CVFP') || stristr($chart->chart_code, 'IAP'));
                        })->sortBy(function ($chart) {
                            return $chart->chart_code;
                        })->toArray();
                        $capp = Charts::removeId($capp);
                        $data_a = [
                            "DP" => $dp,
                            "STAR" => $star,
                            "CAPP" => $capp
                        ];
                    } else {
                        return response()->json(['status' => 'error', 'status_code' => '403', 'message' => 'That is not a valid grouping code.'], 403);
                    }
                } else {
                    $data_a = $all_charts0->toArray();
                    $data_a = Charts::removeId($data_a);
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
     *             @SWG\Property(property="pdf_path", description="URL to the chart comparison PDF", type="string", example="https://charts.aviationapi.com/AIRAC_190103/DDTPPE_190103/compare_pdf/00078WEAZL_CMP.PDF")
     *         )
     *     )
     * )
     */
    public function returnChartChanges(Request $request) {
        $airport_id = strtoupper($request->apt);
        $chart = strtoupper($request->chart_name);
        if($airport_id != null || $chart != null || $airport_id != '%' || $chart != '%') {
            if($airport_id != null && $chart != null) {
                if(strlen($airport_id) == 3) {
                    $airport_id = 'K'.$airport_id;
                }
                $data = Charts::getByKey('CURRENTCHANGECHART')->filter(function ($c) use ($chart){
                    return false != stristr($c->chart_name, $chart);
                })->where('icao_ident', $airport_id)->toArray();
            } elseif($chart != null) {
                $data = Charts::getByKey('CURRENTCHANGECHART')->filter(function ($c) use ($chart){
                    return false != stristr($c->chart_name, $chart);
                })->toArray();
            } else {
                if(strlen($airport_id) == 3) {
                    $airport_id = 'K'.$airport_id;
                }
                $data = Charts::getByKey('CURRENTCHANGECHART')->where('icao_ident', $airport_id)->toArray();
            }
            $data = Charts::removeId($data);
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
     *     @SWG\Parameter(name="apt", in="query", description="FAA or ICAO airport identifier (KCLT or CLT)", required=true, type="string"),
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
     *             @SWG\Property(property="state", description="Full name of the state the facility resides in", type="string", example="NORTH CAROLINA"),
     *             @SWG\Property(property="city", description="Name of the city the facility resides in", type="string", example="CHARLOTTE"),
     *             @SWG\Property(property="airport_name", description="Name of the facility", type="string", example="CHARLOTTE/DOUGLAS INTL"),
     *             @SWG\Property(property="icao_ident", description="Facility ICAO identifier", type="string", example="KCLT"),
     *             @SWG\Property(property="pdf_name", description="Name of the AFD PDF", type="string", example="SE_269_03JAN2019.PDF"),
     *             @SWG\Property(property="pdf_path", description="URL to the AFD PDF", type="string", example="https://charts.aviationapi.com/AFD/AIRAC_190103/2_single_page_PDFs/SE_269_03JAN2019.PDF")
     *         )
     *     )
     * )
     */
    public function returnAFD(Request $request) {
        $airport_id = strtoupper($request->apt);
        if($airport_id != null && $airport_id != '%') {
            if(strlen($airport_id) == 3) {
                $airport_id = 'K'.$airport_id;
            }
            $data = Charts::getByKey('AFD')->where('icao_ident', $airport_id)->toArray();

            $data = Charts::removeId($data);
            return response()->json($data);
        } else {
            return response()->json(['status' => 'error', 'status_code' => '404', 'message' => 'Please specify an airport.'], 404);
        }
    }
}
