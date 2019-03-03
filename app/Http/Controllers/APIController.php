<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
* Class ApiController
*
* @package App\Http\Controllers
*
* @SWG\Swagger(
*     basePath="/v1",
*     host="api.aviationapi.com",
*     schemes={"https"},
*     @SWG\Info(
*         version="1.0",
*         title="AviationAPI API",
*         description="AviationAPI was created for the use on VATSIM, but can be used for any web application. Please check the valid date for ANY aeronautical information prior to use.
          <br><br>
          No authentication is required and the API is open to anyone and everyone. If you are looking for the website for AviationAPI, that can be found at https://www.aviationapi.com.
          <br><br>
          Lastly, AviationAPI will always be free and available to everyone. Hosting an API is not free and all donations of any amount are always welcome at https://www.aviationapi.com/donate.
          <br><br>
          Enjoy!",
*         x={
*           "logo": {
*             "url": "https://www.aviationapi.com/photos/aviationapi_logo.png",
*           },
*         },
*         @SWG\Contact(name="Ian Cowan", url="https://www.aviationapi.com"),
*     ),
*     @SWG\Tag(name="charts",description="Get charts and chart changes with different sorting and searching options, automatically updated every 28 days"),
*     @SWG\Tag(name="airports",description="Get airport data, automatically updated daily"),
*     @SWG\Tag(name="preferred routes",description="Get FAA preferred routes, automatically updated daily"),
*     @SWG\Tag(name="weather",description="Get weather information (METAR/TAF) for any airport in the world, automatically updates on request"),
*     @SWG\Tag(name="VATSIM",description="Get VATSIM pilot and controller connections, automatically updated every two minutes"),
* )
*/

class APIController extends Controller
{
    public function __construct()
    {
        //Log
    }

    /**
    *
    * @SWG\Definition(
    *     definition="error",
    *     type="object",
    *     @SWG\Property(
    *         property="status",
    *         type="string",
    *         example="error",
    *     ),
    *     @SWG\Property(
    *         property="status_code",
    *         type="integer",
    *         example="404",
    *     ),
    *     @SWG\Property(
    *         property="message",
    *         type="string"
    *     ),
    * ),
    * @SWG\Definition(
    *     definition="OK",
    *     type="object",
    *     @SWG\Property(
    *         property="status",
    *         type="string",
    *         example="OK",
    *     ),
    * ),
    * @SWG\Definition(
    *     definition="OKID",
    *     type="object",
    *     @SWG\Property(
    *         property="status",
    *         type="string",
    *         example="OK",
    *     ),
    *     @SWG\Property(
    *         property="id",
    *         type="integer",
    *         example=0,
    *     ),
    * ),
    */
}
