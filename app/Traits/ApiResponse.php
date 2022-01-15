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

    public function error($status, $message, $errors = null)
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

    public function authenticationError($message = 'Invalid email / password'){
        return $this->error(Response::HTTP_UNAUTHORIZED, $message);
    }

    public function validationError($errors)
    {
        return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, __('messages.validation_error'), $errors);
    }

    public function serverError()
    {
        return $this->error(Response::HTTP_INTERNAL_SERVER_ERROR, __('messages.internal-server-error'));
    }
}
