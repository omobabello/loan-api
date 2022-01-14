<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait ApiResponse
{
    public function response($status, $message, $data = null)
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public function error($status, $errors)
    {
        return response()->json([
            'message' => __('messages.validation_error'),
            'errors' => $errors
        ], $status);
    }

    public function validationError($errors)
    {
        return response()->json([
            'message' => __('messages.validation_error'),
            'errors' => $errors
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function serverError()
    {
        return response()->json([
            'message' => __('messages.internal-server-error'),
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
