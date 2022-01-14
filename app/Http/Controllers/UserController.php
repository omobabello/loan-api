<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\UserRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    //

    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

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

            $user = $this->userRepository->register($request);
            return $this->response(Response::HTTP_OK, __('messages.record-created'), $user);
        } catch (ValidationException $err) {
            return $this->validationErrorResponse($err->errors());
        } catch (Exception $err) {
            Log::error($err->getMessage(), $err->getTrace());
            return $this->serverErrorResonse();
        }
    }
}
