<?php

namespace App\Http\Controllers;

use App\Mail\UserRegisteredMail;
use App\Repositories\Contracts\UserRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class UserController extends Controller
{
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

            DB::beginTransaction(); 

            $user = $this->userRepository->register($request);
            $verification = $this->userRepository->createUserVerificationLink($user);

            DB::commit(); 
            // Mail::queue(new UserRegisteredMail($user->email, $user->first_name, env('APP_URL') . "/user/$verification->user_id/confirm/$verification->verification_hash"));
            return $this->response(Response::HTTP_CREATED, __('messages.record-created'), $user);
        } catch (ValidationException $err) {
            return $this->validationError($err->errors());
        } catch (Exception $err) {
            DB::rollBack();
            Log::error($err->getMessage(), $err->getTrace());
            return $this->serverError();
        }
    }

    public function confirm(Request $request, $id, $hash)
    {
        try {
            $hasConfirmed = $this->userRepository->confirmUser($id, $hash);
            return $this->response(Response::HTTP_OK, __('messages.record-created'), $hasConfirmed);
        } catch (NotFoundResourceException | ModelNotFoundException $err) {
            return $this->error(Response::HTTP_NOT_FOUND, __('messages.resource-not-found'));
        }catch (Exception $err) {
            Log::error($err->getMessage(), $err->getTrace());
            return $this->serverError();
        }
    }
}
