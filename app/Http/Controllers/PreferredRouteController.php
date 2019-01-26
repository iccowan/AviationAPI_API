<?php

namespace App\Http\Controllers;

use App\Routing;
use Illuminate\Http\Request;

class PreferredRouteController extends Controller
{
    /**
    *   API response for showing all preferred routes
    *
    *   path = /v1/preferred-routes
    *   summary = "Gets a list of all the preferred routes"
    *   description = "Gets a list of all the preferred routes"
    *   produces = {"application/json"}
    *   tags = {"preferred-routes"}
    *   examples = {
    *       "application/json":[
    *             {"id":1,"origin":"ABE","route":"ABE FJC ARD CYN ACY","destination":"ACY","hours1":null,"hours2":null,"hours3":null,"type":"TEC","area":null,"altitude":"5000","aircraft":null,"flow":null,"seq":1,"d_artcc":"ZNY","a_artcc":"ZDC","created_at":"2019-01-14 04:56:02","updated_at":"2019-01-14 04:56:02"}
    *        ]
    *   }
    *
    **/
    public function showAllRoutes() {
        $data = Routing::get()->toArray();
        return response()->json($data);
    }

    /**
    *   API response for searching the database of preferred routes
    *
    *   path = /v1/preferred-routes/search
    *   summary = "Searches preferred routes"
    *   description = "Searches preferred routes"
    *   produces = {"application/json"}
    *   tags = {"preferred-routes"}
    *   examples = {
    *       "application/json":[
    *             {"id":1,"origin":"ABE","route":"ABE FJC ARD CYN ACY","destination":"ACY","hours1":null,"hours2":null,"hours3":null,"type":"TEC","area":null,"altitude":"5000","aircraft":null,"flow":null,"seq":1,"d_artcc":"ZNY","a_artcc":"ZDC","created_at":"2019-01-14 04:56:02","updated_at":"2019-01-14 04:56:02"}
    *        ]
    *   }
    *
    **/
    public function searchRoutes(Request $request) {
        $origin = $request->origin;
        $dest = $request->dest;
        $type = $request->type;
        $alt = $request->alt;
        $lower_alt = $request->lower_alt;
        $upper_alt = $request->upper_alt;
        $aircraft = $request->aircraft;
        $d_artcc = $request->d_artcc;
        $a_artcc = $request->a_artcc;
        $data = Routing::query();

        if(isset($origin)) {
            $data = $data->where('origin', $origin);
        }
        if(isset($dest)) {
            $data = $data->where('destination', $dest);
        }
        if(isset($type)) {
            $data = $data->where('type', $type);
        }
        if(isset($alt)) {
            $data = $data->where('altitude', $alt)->orWhere('altitude', null);
        }
        if(isset($lower_alt)) {
            $data = $data->where('altitude', '>', $lower_alt)->orWhere('altitude', null);
        }
        if(isset($upper_alt)) {
            $data = $data->where('altitude', '<', $upper_alt)->orWhere('altitude', null);
        }
        if(isset($aircraft)) {
            $data = $data->where('aircraft', 'LIKE', '%'.$aircraft.'%')->where('aircraft', 'NOT LIKE', '%non-'.$aircraft.'%')->where('area', 'NOT LIKE', '%non '.$aircraft.'%')->orWhere('aircraft', null);
        }
        if(isset($d_artcc)) {
            $data = $data->where(function($d) use ($d_artcc) {
                $d->where('d_artcc', $d_artcc)
                  ->orWhere('origin', $d_artcc);
            });
        }
        if(isset($a_artcc)) {
            $data = $data->where(function($d) use ($a_artcc) {
                $d->where('a_artcc', $a_artcc)
                  ->orWhere('destination', $a_artcc);
            });
        }

        $data = $data->get()->toArray();

        return response()->json($data);
    }
}
