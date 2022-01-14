<?php 

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserRepository implements UserRepositoryInterface {

    public function register(Request $request)
    {
        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $data['email_confirmed'] = false;
        $user = User::create($data);
        $this->createUserVerificationLink($user);
        return $request;
    }

    public function createUserVerificationLink(User $user)
    {
        $verification_hash = Hash::make(Str::random(20)); 
        Log::info("verification hash: ${$verification_hash}");
        return $verification_hash;
    }
}