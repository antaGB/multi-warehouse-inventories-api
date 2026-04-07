<?php

namespace App\Traits;

trait ApiResponse {
    protected function success($data, $message = null, $code = 200) {
        return response()->json([
            'status' => 'Success',
            'code'    => $code,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function error($message, $code) {
        return response()->json([
            'status' => 'Error',
            'message' => $message,
            'data' => null
        ], $code);
    }
}