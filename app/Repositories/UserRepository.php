<?php

namespace App\Repositories;

use App\Mail\UserRegisteredMail;
use App\Models\User;
use App\Models\UserVerification;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserRepository implements UserRepositoryInterface
{

    public function register(Request $request)
    {
        $data = $request->all();
        $data['email_confirmed'] = false;
        $user = User::create($data);
        return $user;
    }

    public function createUserVerificationLink(User $user)
    {
        $verification = UserVerification::create(array(
            'user_id' => $user->id,
            'verification_hash' => md5(Str::random(20)),
            'expiry' => date('Y-m-d H:i:s', strtotime('+ 30 mins')),
            'status' => false,
        ));

        return $verification;
    }

    public function confirmUser($userId, $hash)
    {
        $user = User::findOrFail($userId);
        $verification = UserVerification::where('verification_hash', $hash)->firstOrFail();
        if (strtotime('now') > strtotime($verification->expiry)) {
            abort(Response::HTTP_NOT_FOUND);
        }
        $user->email_confirmed = true;
        $user->save();
        $verification->status = true;
        $verification->save();
        return true;
    }

    public function login(Request $request)
    {
        $token = Auth::attempt($request->only('email', 'password'));
        if (!$token) {
            throw new AuthenticationException();
        }
        return [
            'user' => Auth::user(),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ];
    }
}
