<?php 

namespace App\Repositories;

use App\Models\User;
use App\Models\UserVerification;
use App\Repositories\Contracts\UserRepositoryInterface;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserRepository implements UserRepositoryInterface {

    public function register(Request $request)
    {
        $data = $request->all();
        $data['email_confirmed'] = false;
        $user = User::create($data);
        $this->createUserVerificationLink($user);
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
        Log::info("Verification Link : $verification->user_id/$verification->verification_hash");
    }
}