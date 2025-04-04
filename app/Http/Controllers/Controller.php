<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Response;

abstract class Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    static $HTTP_OK = 200;
    static $HTTP_UNPROCESSABLE_ENTITY = 422;
    static $HTTP_CONFLICT = 409;

    /**
     * Executes and returns well formatted json of errors
     * that occurred during validation
     *
     * @param [string] $message
     * @param [collection] $errors
     * @return json
     */
    public function validationFailed($message, $errors)
    {
        $info = [
            "status" =>  self::$HTTP_UNPROCESSABLE_ENTITY,
            'message' => $message,
            'errors' => $errors,
        ];
        return Response::json($info, self::$HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Returns json stating why data creation failed
     * @param [string] $message
     * @return json
     */
    public function actionSuccess($message, $data = null)
    {
        $info = [
            "status" => self::$HTTP_OK,
            'message' => $message,
        ];
        if ($data != null) {
            $info['data'] = $data;
        }
        return Response::json($info, self::$HTTP_OK);
    }

    /**
     * Returns json stating why data creation failed
     * @param [string] $message
     * @return json
     */
    public function actionFailure($message,$data = null)
    {
        $info = [
            "status" => self::$HTTP_CONFLICT,
            'message' => $message,
        ];

        if ($data != null) {
            $info['data'] = $data;
        }
        return response($info, self::$HTTP_CONFLICT);
    }
}
