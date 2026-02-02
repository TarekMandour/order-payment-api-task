<?php

namespace App\Traits;

trait ApiResponse
{
    protected function success($message = 'Success', $data = null, $status = 200) {
        return response()->json([
            'success'  => true,
            'message' => $message,
            'data'    => $data,
            'errors' => null
        ], $status);
    }

    protected function error($message = null, $errors = null, $status = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors
        ], $status);
    }

}
