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
    *       "application/json":{
    *             {"id":1,"origin":"ABE","route":"ABE FJC ARD CYN ACY","destination":"ACY","hours1":null,"hours2":null,"hours3":null,"type":"TEC","area":null,"altitude":"5000","aircraft":null,"flow":null,"seq":1,"d_artcc":"ZNY","a_artcc":"ZDC","created_at":"2019-01-14 04:56:02","updated_at":"2019-01-14 04:56:02"},
    *             {"id":2,"origin":"ABE","route":"ABE FJC LAAYK ALB","destination":"ALB","hours1":null,"hours2":null,"hours3":null,"type":"TEC","area":null,"altitude":"7000","aircraft":null,"flow":null,"seq":1,"d_artcc":"ZNY","a_artcc":"ZBW","created_at":"2019-01-14 04:56:02","updated_at":"2019-01-14 04:56:02"}
    *        }
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
    *       "application/json":{
    *             {"id":1,"origin":"ABE","route":"ABE FJC ARD CYN ACY","destination":"ACY","hours1":null,"hours2":null,"hours3":null,"type":"TEC","area":null,"altitude":"5000","aircraft":null,"flow":null,"seq":1,"d_artcc":"ZNY","a_artcc":"ZDC","created_at":"2019-01-14 04:56:02","updated_at":"2019-01-14 04:56:02"},
    *             {"id":2,"origin":"ABE","route":"ABE FJC LAAYK ALB","destination":"ALB","hours1":null,"hours2":null,"hours3":null,"type":"TEC","area":null,"altitude":"7000","aircraft":null,"flow":null,"seq":1,"d_artcc":"ZNY","a_artcc":"ZBW","created_at":"2019-01-14 04:56:02","updated_at":"2019-01-14 04:56:02"}
    *        }
    *   }
    *
    **/
    public function searchRoutes(Request $request) {
        $origin = $request->origin;
        $dest = $request->dest;
        $data = Routing::query();

        if (isset($origin)) {
            $data = $data->where('origin', $origin);
        }
        if (isset($dest)) {
            $data = $data->where('destination', $dest);
        }
        $data = $data->get()->toArray();

        return response()->json($data);
    }
}
