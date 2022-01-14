<?php

namespace App\Traits;

trait ApiResponse
{
    public function response($status, $message, $data = null, $errors = null)
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
            'errors' => $errors
        ], $status);
    }
}
