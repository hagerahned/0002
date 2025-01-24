<?php

namespace App\Helpers;

class ApiResponse
{
    static function sendResponse($msg = null, $data = null)
    {
        $response = array(
            'message' => $msg,
            'data' => $data
        );

        return response()->json($response);
    }
}
