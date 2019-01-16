<?php

namespace App\Http\Controllers;

use App\CurrentChart;
use Illuminate\Http\Request;

class ChartsController extends Controller
{
    public function returnCharts(Request $request) {
        $airport_id = $request->apt;
        if(isset($airport_id)) {
            $data = CurrentChart::where('icao_ident', $airport_id)->get()->toArray();
            return response()->json($data);
        } else {
            return 'error';
        }
    }
}
