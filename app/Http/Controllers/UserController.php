<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    //

    public function store(Request $request)
    {

        try {
            $this->validate($request, [
                'first_name' => 'required|name',
                'last_name' => 'required|name',
                'email' => 'required|email|unique:users',
                'phone' => 'required|phone|unique:users',
                'password' => 'required|min:5|max:255',
                'account_type' => 'required|account_type',
                'address' => 'required'
            ]);

            return $this->response(Response::HTTP_OK, 'Good', ['bednt']);
        } catch (ValidationException $err) {
            return $this->validationErrorResponse($err->errors());
        } catch (Exception $err) {
            Log::error($err->getMessage(), $err->getTrace());
            return $this->serverErrorResonse();
        }
    }

    private function validateAccountType()
    {
        return false;
    }
}
