<?php

namespace App\Exceptions;

use \Config;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if(Config::get('app.debug') == true) {
            if($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                return response()->json(["status" => "error", "status_code" => "404", "message" => "That could not be found."], 404);
            } elseif($exception instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException) {
                return response()->json(["status" => "error", "status_code" => "403", "message" => "Forbidden."], 403);
            } elseif($exception instanceof \Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException) {
                return response()->json(["status" => "error", "status_code" => "503", "message" => "Service temporarily unavailable."], 503);
            }
        } else {
            if($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                return response()->json(["status" => "error", "status_code" => "404", "message" => "That could not be found."], 404);
            } elseif($exception instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException) {
                return response()->json(["status" => "error", "status_code" => "403", "message" => "Forbidden."], 403);
            } elseif($exception instanceof \Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException) {
                return response()->json(["status" => "error", "status_code" => "503", "message" => "Service temporarily unavailable."], 503);
            } else {
                return response()->json(["status" => "error", "status_code" => "500", "message" => "Whoops, something went wrong."], 500);
            }
        }


        return parent::render($request, $exception);
    }
}
