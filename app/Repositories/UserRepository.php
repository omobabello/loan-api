<?php 

namespace App\Repositories;

use App\Mail\UserRegisteredMail;
use App\Models\User;
use App\Models\UserVerification;
use App\Repositories\Contracts\UserRepositoryInterface;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

        Mail::queue(new UserRegisteredMail($user->email, $user->first_name, env('APP_URL')."/user/$verification->user_id/confirm/$verification->verification_hash"));

        // Mail::queue();

        Log::info("Verification Link : ".env('APP_URL')."/$verification->user_id/$verification->verification_hash");
    }
}