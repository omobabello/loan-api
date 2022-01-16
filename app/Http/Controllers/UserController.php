<?php

namespace App\Http\Controllers;

use App\Mail\UserRegisteredMail;
use App\Repositories\Contracts\UserRepositoryInterface;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
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

    public function index(Request $request)
    {
        try {
            $users = $this->userRepository->getUsers();
            return $this->response(Response::HTTP_OK, __('messages.records-fetched'), $users);
        } catch (Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return $this->serverError();
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $user = $this->userRepository->getUser($id);
            return $this->response(Response::HTTP_OK, __('messages.record-fetched'), $user);
        } catch (NotFoundResourceException | ModelNotFoundException $err) {
            return $this->error(Response::HTTP_NOT_FOUND, __('messages.resource-not-found'));
        } catch (Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return $this->serverError();
        }
    }

    public function login(Request $request)
    {
        try {

            $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = $this->userRepository->login($request);
            return $this->response(Response::HTTP_OK, __('messages.login-successful'), $user);
        } catch (ValidationException $err) {
            return $this->validationError($err->errors());
        } catch (AuthenticationException $e) {
            return $this->authenticationError('Invalid email / password');
        } catch (Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return $this->serverError();
        }
    }

    public function logout()
    {
        try {
            Auth::logout();
            return $this->response(Response::HTTP_OK, __('messages.logout-successful'));
        } catch (Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return $this->serverError();
        }
    }
    /**
     * Refresh the current token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            $token = [
                'token' => Auth::refresh(),
                'token_type' => 'bearer',
                'expires_in' => Auth::factory()->getTTL() * 60
            ];
            return $this->response(Response::HTTP_OK, __('messages.record-fetched'), $token);
        } catch (Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return $this->serverError();
        }
    }

    public function register(Request $request)
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
        } catch (Exception $err) {
            Log::error($err->getMessage(), $err->getTrace());
            return $this->serverError();
        }
    }
}
