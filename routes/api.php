<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Version 1
Route::prefix('/v1')->group(function() {
    Route::prefix('/preferred-routes')->group(function() {
        Route::get('/', 'PreferredRouteController@showAllRoutes');
        Route::get('/search', 'PreferredRouteController@searchRoutes');
    });
    Route::prefix('/charts')->group(function() {
        Route::get('/', 'ChartsController@returnCharts');
        Route::get('/changes', 'ChartsController@returnChartChanges');
        Route::get('/afd', 'ChartsController@returnAFD');
    });
    Route::prefix('/airports')->group(function() {
        Route::get('/', 'AirportDataController@getAllAirports');
        Route::get('/search', 'AirportDataController@searchByAirportName');
    });
    Route::prefix('/weather')->group(function() {
        Route::get('/metar', 'WeatherController@searchMetar');
        Route::get('/taf', 'WeatherController@searchTaf');
    });
});
