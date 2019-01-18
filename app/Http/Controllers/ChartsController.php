<?php

namespace App\Http\Controllers;

use App\CurrentChart;
use App\CurrentChangeChart;
use Illuminate\Http\Request;

class ChartsController extends Controller
{
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
            return response()->json(['status' => 'error', 'message' => 'Please specify an airport.'], 500);
        }
    }

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
