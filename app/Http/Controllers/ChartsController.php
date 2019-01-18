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
        if(isset($airport_id)) {
            if(strlen($airport_id) == 3) {
                $airport_id = 'K'.$airport_id;
            }
            $data = CurrentChart::where('icao_ident', $airport_id)->get()->toArray();
/*
            if(isset($group)) {
                if($group == 1) {
                    //Groups by type
                    $general = $data->where('chart_code', 'APD')->orWhere('chart_code', 'MIN')->orWhere('chart_code', 'LAH')->orWhere('chart_code', 'HOT')->orderBy('chart_code', 'ASC')->get()->toArray();
                    $dp = $data->where('chart_code', 'DP')->get()->toArray();
                    $star = $data->where('chart_code', 'STAR')->get()->toArray();
                    $capp = $data->where('chart_code', 'IAP')->orWhere('chart_code', 'CVFP')->orderBy('chart_code', 'ASC')->get()->toArray();
                    $data = [
                        "General" => $general,
                        "DP" => $dp,
                        "STAR" => $star,
                        "CAPP" => $capp
                    ];
                } elseif($group == 2) {
                    //APD only
                    $data = $data->where('chart_code', 'APD')->get()->toArray();
                } elseif($group == 3) {
                    //General only
                    $data = $data->where('chart_code', 'APD')->orWhere('chart_code', 'MIN')->orWhere('chart_code', 'LAH')->orWhere('chart_code', 'HOT')->orderBy('chart_code', 'ASC')->get()->toArray();
                } elseif($group == 4) {
                    //DP only
                    $data = $data->where('chart_code', 'DP')->get()->toArray();
                } elseif($group == 5) {
                    //STAR only
                    $data = $data->where('chart_code', 'STAR')->get()->toArray();
                } elseif($group == 6) {
                    //CAPP only
                    $data = $data->where('chart_code', 'IAP')->orWhere('chart_code', 'CVFP')->orderBy('chart_code', 'ASC')->get()->toArray();
                } elseif($group == 7) {
                    //Groups by DP, STAR, CAPP only
                    $dp = $data->where('chart_code', 'DP')->get()->toArray();
                    $star = $data->where('chart_code', 'STAR')->get()->toArray();
                    $capp = $data->where('chart_code', 'IAP')->orWhere('chart_code', 'CVFP')->orderBy('chart_code', 'ASC')->get()->toArray();
                    $data = [
                        "DP" => $dp,
                        "STAR" => $star,
                        "CAPP" => $capp
                    ];
                }
            }
*/

            return response()->json($data);
        } else {
            return 'error';
        }
    }
/*
    public function returnChartChanges(Request $request) {
        $airport_id = $request->apt;
        $chart = $request->chart_name;
        if(isset($airport_id) || isset($chart)) {
            if(isset($airport_id) && isset($chart)) {
                $data = CurrentChangeChart::where('chart_name', 'LIKE', '%'.$chart.'%')->where('icao_ident', $airport_id)->get()->toArray();
            } elseif(isset($chart)) {
                $data = CurrentChangeChart::where('chart_name', 'LIKE', '%'.$chart.'%')->get()->toArray();
            } else {
                $data = CurrentChangeChart::where('icao_ident', $airport_id)->get()->toArray();
            }
            return response()->json($data);
        } else {
            return 'error';
        }
    }
*/
}
