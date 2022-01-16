<?php

namespace App\Listeners;

use App\Events\UserCreatedEvent;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\WalletRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class UserCreatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    private $walletRepository;
    private $userRepository;

    public function __construct(WalletRepositoryInterface $walletRepository, UserRepositoryInterface $userRepository)
    {
        $this->walletRepository =  $walletRepository;
        $this->$userRepository = $userRepository;
    }

    /**
     * Handle the event.
     *
     * @param  UserCreatedEvent  $event
     * @return void
     */
    public function handle(UserCreatedEvent $event)
    {
        $this->walletRepository->create($event->user->id);
        
        $verification = $this->userRepository->createUserVerificationLink($event->user);
        // Mail::queue(new UserRegisteredMail($user->email, $user->first_name, env('APP_URL') . "/user/$verification->user_id/confirm/$verification->verification_hash"));

    }

    public function failed($event, Exception $e)
    {
        Log::error($e->getMessage(), $e->getTrace());
    }
}
