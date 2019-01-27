<?php

namespace App\Http\Controllers;

use App\AirportData;
use Illuminate\Http\Request;

class AirportDataController extends Controller
{
    /**
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse|string
     *
     * @SWG\Get(
     *     path="/airports",
     *     summary="Get an airport by its ICAO or FAA identifier",
     *     description="Search for an airport by its ICAO or FAA identifier",
     *     produces={"application/json"},
     *     tags={"airports"},
     *     @SWG\Parameter(name="apt", in="query", description="FAA or ICAO facility identifier (KAVL or AVL). Separate multiple entries with a comma", required=true, type="string"),
     *     @SWG\Response(
     *         response="404",
     *         description="No airport found in search",
     *         @SWG\Schema(ref="#/definitions/error"),
     *         examples={"application/json":{"status":"error","status_code":"404","message":"You must search by at least one parameter"}}
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="OK",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(property="id", type="integer", example="10159"),
     *             @SWG\Property(property="site_number", description="FAA unique airport site number", type="string", example="16517.5*A"),
     *             @SWG\Property(property="type", description="Type of facility (AIRPORT/HELIPORT)", type="string", example="AIRPORT"),
     *             @SWG\Property(property="facility_name", description="Name of the facility", type="string", example="ASHEVILLE RGNL"),
     *             @SWG\Property(property="faa_ident", description="FAA identifier", type="string", example="AVL"),
     *             @SWG\Property(property="icao_ident", description="ICAO identifier", type="string", example="KAVL"),
     *             @SWG\Property(property="district_office", description="FSDO of jurisdiction", type="string", example="MEM"),
     *             @SWG\Property(property="state", description="Two letter abbreviation of the state the facility resides in", type="string", example="NC"),
     *             @SWG\Property(property="state_full", description="Full name of the state the facility resides in", type="string", example="NORTH CAROLINA"),
     *             @SWG\Property(property="county", description="Name of the county the facility resides in", type="string", example="BUNCOMBE"),
     *             @SWG\Property(property="city", description="Name of the city the facility resides in", type="string", example="ASHEVILLE"),
     *             @SWG\Property(property="ownership", description="Facility ownership, PU -> Public, PR -> Private", type="string", example="PU"),
     *             @SWG\Property(property="use", description="Facility use, PU -> Public, PR -> Private", type="string", example="PU"),
     *             @SWG\Property(property="manager", description="Name of the facility's manager", type="string", example="LEW S. BLEIWEIS, A.A.E."),
     *             @SWG\Property(property="manager_phone", description="Phone number of the facility's manager", type="string", example="828-684-2226"),
     *             @SWG\Property(property="latitude", description="Latitude of the facility", type="string", example="35-26-04.0000N"),
     *             @SWG\Property(property="latitude_sec", description="Latitude in seconds of the facility", type="string", example="127564.0000N"),
     *             @SWG\Property(property="longitude", description="Longitude of the facility", type="string", example="082-32-33.8240W"),
     *             @SWG\Property(property="longitude_sec", description="Longitude in seconds of the facility", type="string", example="297153.8240W"),
     *             @SWG\Property(property="elevation", description="Elevation of the facility", type="string", example="2162"),
     *             @SWG\Property(property="magnetic_variation", description="Magnetic variation at the facility", type="string", example="07W"),
     *             @SWG\Property(property="tpa", description="Traffic pattern altitude, Blank -> Standard", type="string", example="3162"),
     *             @SWG\Property(property="vfr_sectional", description="Name of the VFR sectional the facility lies on", type="string", example="ATLANTA"),
     *             @SWG\Property(property="boundary_artcc", description="ARTCC that bounds the facility", type="string", example="ZTL"),
     *             @SWG\Property(property="boundary_artcc_name", description="Name of the ARTCC that bounds the facility", type="string", example="ATLANTA"),
     *             @SWG\Property(property="responsible_artcc", description="ARTCC that is responsible for the facility, Blank -> Same as boundary", type="string", example="ZTL"),
     *             @SWG\Property(property="responsible_artcc_name", description="Name of the ARTCC that is responsible for the facility, Blank -> Same as boundary", type="string", example="ATLANTA"),
     *             @SWG\Property(property="fss_phone_number", description="Local flight service station phone number", type="string", example="1-828-WX-BRIEF"),
     *             @SWG\Property(property="fss_phone_numer_tollfree", description="Tollfree flight service station phone number", type="string", example="1-800-WX-BRIEF"),
     *             @SWG\Property(property="notam_facility_ident", description="Facility identifier for the NOTAM database", type="string", example="AVL"),
     *             @SWG\Property(property="status", description="Facility status, O -> Open, C -> Closed", type="string", example="O"),
     *             @SWG\Property(property="certification_typedate", description="Type and date of the facility's certification", type="string", example="I B S 05/1973"),
     *             @SWG\Property(property="customs_airport_of_entry", description="Customs airport of entry, Y -> Yes, N -> No", type="string", example="N"),
     *             @SWG\Property(property="military_joint_use", description="Military join use of facility, Y -> Yes, N -> No", type="string", example="N"),
     *             @SWG\Property(property="military_landing", description="Military authorized for landing at facility, Y -> Yes, N -> No", type="string", example="Y"),
     *             @SWG\Property(property="lighting_schedule", description="Lighting schedule at the facility, if applicable", type="string", example="SEE RMK"),
     *             @SWG\Property(property="beacon_schedule", description="Beacon schedule at the facility", type="string", example="SS-SR"),
     *             @SWG\Property(property="control_tower", description="Control tower present at the facility, Y -> Yes, N -> No", type="string", example="Y"),
     *             @SWG\Property(property="unicom", description="Facility UNICOM frequency", type="string", example="122.950"),
     *             @SWG\Property(property="ctaf", description="Facility CTAF", type="string", example="121.100"),
     *             @SWG\Property(property="effective_date", description="Effective date of this data", type="string", example="01/03/2019"),
     *             @SWG\Property(property="created_at", type="timestamp", example="2019-01-26 21:18:00"),
     *             @SWG\Property(property="updated_at", type="timestamp", example="2019-01-26 21:18:00")
     *         )
     *     )
     * )
     */
    public function searchByAirportName(Request $request) {
        $airport = $request->apt;
        $airport_array = explode(',', $airport);
        $b_artcc = $request->boundary;
        $r_artcc = $request->responsible;
        if(isset($airport) && isset($b_artcc) && isset($r_artcc)) {
            return response()->json(['status' => 'error', 'status_code' => '500', 'message' => 'You must search by only one parameter'], 500);
        } elseif(isset($airport) && isset($b_artcc)) {
            return response()->json(['status' => 'error', 'status_code' => '500', 'message' => 'You must search by only one parameter'], 500);
        } elseif(isset($b_artcc) && isset($r_artcc)) {
            return response()->json(['status' => 'error', 'status_code' => '500', 'message' => 'You must search by only one parameter'], 500);
        } elseif(isset($airport) && isset($r_artcc)) {
            return response()->json(['status' => 'error', 'status_code' => '500', 'message' => 'You must search by only one parameter'], 500);
        }

        if($airport) {
            $data = array();
            foreach($airport_array as $apt) {
                $apt = strtoupper($apt);
                if(strlen($apt) < 4) {
                    $data_a = AirportData::where('faa_ident', $apt)->get()->toArray();
                } else {
                    $data_a = AirportData::where('icao_ident', $apt)->get()->toArray();
                }
                $data[$apt] = $data_a;
            }
        } elseif(isset($b_artcc)) {
            $b_artcc = strtoupper($b_artcc);
            if(strlen($b_artcc) == 3) {
                $data[$apt] = AirportData::where('boundary_artcc', $b_artcc)->get()->toArray();
            } else {
                $data[$apt] = AirportData::where('boundary_artcc_name', $b_artcc)->get()->toArray();
            }
        } elseif(isset($r_artcc)) {
            $r_artcc = strtoupper($r_artcc);
            if(strlen($r_artcc) == 3) {
                $ids = AirportData::where('responsible_artcc', $r_artcc)->orWhere('boundary_artcc', $r_artcc)->get()->filter(function($apt) use($r_artcc) {
                    if(strlen($apt->responsible_artcc) == 0) {
                       return $apt;
                    } elseif(strlen($apt->boundary_artcc) != 0 && strlen($apt->responsible_artcc) != 0) {
                        return $apt->responsible_artcc == $r_artcc;
                    }
                })->pluck('id')->toArray();

                $data[$apt] = AirportData::find($ids)->toArray();
            } else {
                $ids = AirportData::where('responsible_artcc_name', $r_artcc)->orWhere('boundary_artcc_name', $r_artcc)->get()->filter(function($apt) use($r_artcc) {
                    if(strlen($apt->responsible_artcc_name) == 0) {
                       return $apt;
                   } elseif(strlen($apt->boundary_artcc_name) != 0 && strlen($apt->responsible_artcc_name) != 0) {
                        return $apt->responsible_artcc_name == $r_artcc;
                    }
                })->pluck('id')->toArray();

                $data[$apt] = AirportData::find($ids)->toArray();
            }
        } else {
            return response()->json(['status' => 'error', 'status_code' => '404', 'message' => 'You must search for at least one airport.'], 404);
        }

        return response()->json($data);
    }
}
