<?php

namespace App\Http\Controllers;

use App\CurrentChart;
use App\CurrentChangeChart;
use Illuminate\Http\Request;

class ChartsController extends Controller
{

    /**
    *   API response for showing charts for a specific airport with optional grouping
    *
    *   path = /v1/charts
    *   summary = "Gets all of the charts for a specified airport"
    *   description = "Gets all of the charts for a specified airport"
    *   produces = {"application/json"}
    *   tags = {"charts"}
    *   examples = {
    *       "application/json":[
    *             {"id":12899,"state":"NC","state_full":"North Carolina","city":"ASHEVILLE","volume":"SE-2","airport_name":"ASHEVILLE RGNL","military":"N","faa_ident":"AVL","icao_ident":"KAVL","chart_seq":"70000","chart_code":"APD","chart_name":"AIRPORT DIAGRAM","pdf_name":"05061AD.PDF","pdf_path":"https://charts.aviationapi.com/AIRAC_190103/05061AD.PDF","created_at":"2019-01-18 15:52:28","updated_at":"2019-01-18 15:52:28"}
    *       ]
    *   }
    *
    **/
    public function returnCharts(Request $request) {
        $airport_id = strtoupper($request->apt);
        $group = $request->group;
        if($airport_id != null) {
            if(strlen($airport_id) == 3) {
                $airport_id = 'K'.$airport_id;
            }
            $all_charts0 = CurrentChart::where('icao_ident', $airport_id);
            $all_charts1 = CurrentChart::where('icao_ident', $airport_id);
            $all_charts2 = CurrentChart::where('icao_ident', $airport_id);
            $all_charts3 = CurrentChart::where('icao_ident', $airport_id);

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
                    $data = [
                        "General" => $general,
                        "DP" => $dp,
                        "STAR" => $star,
                        "CAPP" => $capp
                    ];
                } elseif($group == 2) {
                    //APD only
                    $data = $all_charts0->where('chart_code', 'APD')->get()->toArray();
                } elseif($group == 3) {
                    //General only
                    $data = $all_charts0->where('chart_code', 'APD')->orWhere('chart_code', 'MIN')->orWhere('chart_code', 'LAH')->orWhere('chart_code', 'HOT')->orderBy('chart_code', 'ASC')->get()->toArray();
                } elseif($group == 4) {
                    //DP only
                    $data = $all_charts0->where('chart_code', 'DP')->get()->toArray();
                } elseif($group == 5) {
                    //STAR only
                    $data = $all_charts0->where('chart_code', 'STAR')->get()->toArray();
                } elseif($group == 6) {
                    //CAPP only
                    $data = $all_charts0->where('chart_code', 'IAP')->orWhere('chart_code', 'CVFP')->orderBy('chart_code', 'ASC')->get()->toArray();
                } elseif($group == 7) {
                    //Groups by DP, STAR, CAPP only
                    $dp = $all_charts0->where('chart_code', 'DP')->get()->toArray();
                    $star = $all_charts1->where('chart_code', 'STAR')->get()->toArray();
                    $capp = $all_charts2->where('chart_code', 'IAP')->orWhere('chart_code', 'CVFP')->orderBy('chart_code', 'ASC')->get()->toArray();
                    $data = [
                        "DP" => $dp,
                        "STAR" => $star,
                        "CAPP" => $capp
                    ];
                } else {
                    return response()->json(['status' => 'error', 'status_code' => '500', 'message' => 'That is not a valid search code.'], 500);
                }
            } else {
                $data = $all_charts0->get()->toArray();
            }

            if(count($data) < 1) {
                return response()->json(null);
            } else {
                return response()->json($data);
            }
        } else {
            return response()->json(['status' => 'error', 'status_code' => '500', 'message' => 'Please specify an airport.'], 500);
        }
    }

    /**
    *   API response for showing chart changes based on either an airport search, chart name search, or both
    *
    *   path = /v1/charts
    *   summary = "Gets all of the chart changes by a specified airport name, chart name, or both"
    *   description = "Gets all of the chart changes by a specified airport name, chart name, or both"
    *   produces = {"application/json"}
    *   tags = {"charts","chart-changes"}
    *   examples = {
    *       "application/json":[
    *             {"id":1120,"state":"NC","state_full":"North Carolina","city":"ASHEVILLE","volume":"SE-2","airport_name":"ASHEVILLE RGNL","military":"N","faa_ident":"AVL","icao_ident":"KAVL","chart_seq":"50750","chart_code":"IAP","chart_name":"ILS OR LOC RWY 35","pdf_name":"05061IL35_CMP.PDF","pdf_path":"https://charts.aviationapi.com/AIRAC_190103/DDTPPE_190103/compare_pdf/05061IL35_CMP.PDF","created_at":"2019-01-18 15:57:12","updated_at":"2019-01-18 15:57:12"}
    *       ]
    *   }
    *
    **/

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
            return response()->json(['status' => 'error', 'message' => 'Please specify either an airport or a procedure name.'], 500);
        }
    }

}
