<?php

namespace App\Http\Controllers;

use App\Routing;
use Illuminate\Http\Request;

class PreferredRouteController extends Controller
{
    /**
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse|string
     *
     * @SWG\Get(
     *     path="/preferred-routes",
     *     summary="Get all of the preferred routes",
     *     description="Get all of the preferred routes with no search parameters",
     *     produces={"application/json"},
     *     tags={"preferred routes"},
     *     @SWG\Response(
     *         response="200",
     *         description="OK",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(property="id", type="integer", example="28"),
     *             @SWG\Property(property="origin", description="Originating airport of the route", type="string", example="ABQ"),
     *             @SWG\Property(property="route", description="Preferred route. May be partial or full", type="string", example="ABQ DIESL TTORO3 IAH"),
     *             @SWG\Property(property="destination", description="Destination airport of the route", type="string", example="IAH"),
     *             @SWG\Property(property="hours1", description="First group of active hours", type="string", example="0900-1100"),
     *             @SWG\Property(property="hours2", description="Second group of active hours", type="string", example="null"),
     *             @SWG\Property(property="hours3", description="Third group of active hours", type="string", example="null"),
     *             @SWG\Property(property="type", description="Type of route, H -> High, L -> low, LSD -> Low Single Direction, HSD -> High Single Direction, SLD -> Special Low Altitude Directional, SHD -> Special High Altitude Directional, TEC -> Tower Enroute Control, Blank -> N/A", type="string", example="H"),
     *             @SWG\Property(property="area", description="Area description", type="string", example="null"),
     *             @SWG\Property(property="altitude", description="Altitude for use with the route", type="string", example="350"),
     *             @SWG\Property(property="aircraft", description="Limitation on the type of aircraft that can fly the route", type="string", example="TURBOJETS & TURBOPROPS - DME/DME/IRU OR GPS"),
     *             @SWG\Property(property="flow", description="Flow requirements for using the route", type="string", example="IAH EAST FLOW"),
     *             @SWG\Property(property="seq", description="Sequence to use the route in", type="integer", example="1"),
     *             @SWG\Property(property="d_artcc", description="Originating airport ARTCC", type="string", example="ZAB"),
     *             @SWG\Property(property="a_artcc", description="Arrival airport ARTCC", type="string", example="ZHU"),
     *             @SWG\Property(property="created_at", type="timestamp", example="2019-01-26 21:18:00"),
     *             @SWG\Property(property="updated_at", type="timestamp", example="2019-01-26 21:18:00")
     *         )
     *     )
     * )
     */
    public function showAllRoutes() {
        $data = Routing::get()->toArray();
        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse|string
     *
     * @SWG\Get(
     *     path="/preferred-routes/search",
     *     summary="Get preferred routes based on search criteria",
     *     description="Search for preferred routes by various parameters. At least one search criteria is required, although a combination of any can be used",
     *     produces={"application/json"},
     *     tags={"preferred routes"},
     *     @SWG\Parameter(name="origin", in="query", description="Originating airport FAA identifier", required=false, type="string"),
     *     @SWG\Parameter(name="dest", in="query", description="Destination airport FAA identifier", required=false, type="string"),
     *     @SWG\Parameter(name="type", in="query", description="Type of route, H -> High, L -> low, LSD -> Low Single Direction, HSD -> High Single Direction, SLD -> Special Low Altitude Directional, SHD -> Special High Altitude Directional, TEC -> Tower Enroute Control, Blank -> N/A", required=false, type="string"),
     *     @SWG\Parameter(name="alt", in="query", description="Hard altitude for the route", required=false, type="string"),
     *     @SWG\Parameter(name="lower_alt", in="query", description="Lower altitude limit", required=false, type="string"),
     *     @SWG\Parameter(name="upper_alt", in="query", description="Upper altitude limit", required=false, type="string"),
     *     @SWG\Parameter(name="aircraft", in="query", description="Type of aircraft for the route", required=false, type="string"),
     *     @SWG\Parameter(name="d_artcc", in="query", description="Departure airport ARTCC three letter identifier", required=false, type="string"),
     *     @SWG\Parameter(name="a_artcc", in="query", description="Arrival airport ARTCC three letter identifier", required=false, type="string"),
     *     @SWG\Response(
     *         response="404",
     *         description="No search parameter",
     *         @SWG\Schema(ref="#/definitions/error"),
     *         examples={"application/json":{"status":"error","status_code":"404","message":"You must search by at least one parameter."}}
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="ICAO identifier used",
     *         @SWG\Schema(ref="#/definitions/error"),
     *         examples={"status":"error","status_code":"500","message":"It appears you searched with an ICAO identifier. Please use the FAA 3 letter identifier."}}
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="OK",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(property="id", type="integer", example="28"),
     *             @SWG\Property(property="origin", description="Originating airport of the route", type="string", example="ABQ"),
     *             @SWG\Property(property="route", description="Preferred route. May be partial or full", type="string", example="ABQ DIESL TTORO3 IAH"),
     *             @SWG\Property(property="destination", description="Destination airport of the route", type="string", example="IAH"),
     *             @SWG\Property(property="hours1", description="First group of active hours", type="string", example="0900-1100"),
     *             @SWG\Property(property="hours2", description="Second group of active hours", type="string", example="null"),
     *             @SWG\Property(property="hours3", description="Third group of active hours", type="string", example="null"),
     *             @SWG\Property(property="type", description="Type of route, H -> High, L -> low, LSD -> Low Single Direction, HSD -> High Single Direction, SLD -> Special Low Altitude Directional, SHD -> Special High Altitude Directional, TEC -> Tower Enroute Control, Blank -> N/A", type="string", example="H"),
     *             @SWG\Property(property="area", description="Area description", type="string", example="null"),
     *             @SWG\Property(property="altitude", description="Altitude for use with the route", type="string", example="350"),
     *             @SWG\Property(property="aircraft", description="Limitation on the type of aircraft that can fly the route", type="string", example="TURBOJETS & TURBOPROPS - DME/DME/IRU OR GPS"),
     *             @SWG\Property(property="flow", description="Flow requirements for using the route", type="string", example="IAH EAST FLOW"),
     *             @SWG\Property(property="seq", description="Sequence to use the route in", type="integer", example="1"),
     *             @SWG\Property(property="d_artcc", description="Originating airport ARTCC", type="string", example="ZAB"),
     *             @SWG\Property(property="a_artcc", description="Arrival airport ARTCC", type="string", example="ZHU"),
     *             @SWG\Property(property="created_at", type="timestamp", example="2019-01-26 21:18:00"),
     *             @SWG\Property(property="updated_at", type="timestamp", example="2019-01-26 21:18:00")
     *         )
     *     )
     * )
     */
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

        if($origin == null && $dest == null && $type == null && $alt == null && $lower_alt == null && $upper_alt == null && $aircraft == null && $d_artcc == null && $a_artcc == null) {
            return response()->json(['status' => 'error', 'status_code' => '404', 'message' => 'You must search by at least one parameter.'], 404);
        }

        if(isset($origin) && $origin != '%') {
            if (strlen($origin) == 4) {
                return response()->json(['status' => 'error', 'status_code' => '500', 'message' => 'It appears you searched with an ICAO identifier. Please use the FAA 3 letter identifier.'], 500);
            }
            $data = $data->where('origin', $origin);
        }
        if(isset($dest) && $dest != '%') {
            if (strlen($dest) == 4) {
                return response()->json(['status' => 'error', 'status_code' => '500', 'message' => 'It appears you searched with an ICAO identifier. Please use the FAA 3 letter identifier.'], 500);
            }
            $data = $data->where('destination', $dest);
        }
        if(isset($type) && $type != '%') {
            $data = $data->where('type', $type);
        }
        if(isset($alt) && $alt != '%') {
            $data = $data->where(function($route) use ($alt){
                return $route->where('altitude', $alt)->orWhere('altitude', null);
            });
        }
        if(isset($lower_alt) && $lower_alt != '%') {
            $data = $data->where(function($route) use($alt) {
                $route->where('altitude', '>', $lower_alt)->orWhere('altitude', null);
            });
        }
        if(isset($upper_alt) && $upper_alt != '%') {
            $data = $data->where(function($route) use($alt) {
                $route->where('altitude', '<', $lower_alt)->orWhere('altitude', null);
            });
        }
        if(isset($aircraft) && $aircraft != '%') {
            $data = $data->where(function($route) use($aircraft) {
                $route->where('aircraft', 'LIKE', '%'.$aircraft.'%')->where('aircraft', 'NOT LIKE', '%non-'.$aircraft.'%')->where('area', 'NOT LIKE', '%non '.$aircraft.'%')->orWhere('aircraft', null);
            });
        }
        if(isset($d_artcc) && $d_artcc != '%') {
            $data = $data->where(function($d) use ($d_artcc) {
                $d->where('d_artcc', $d_artcc)
                  ->orWhere('origin', $d_artcc);
            });
        }
        if(isset($a_artcc) && $a_artcc != '%') {
            $data = $data->where(function($d) use ($a_artcc) {
                $d->where('a_artcc', $a_artcc)
                  ->orWhere('destination', $a_artcc);
            });
        }

        $data = $data->get()->toArray();

        return response()->json($data);
    }
}
