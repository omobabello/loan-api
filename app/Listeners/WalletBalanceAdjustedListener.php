<?php

namespace App\Listeners;

use App\Events\WalletBalanceAdjustedEvent;
use App\Mail\WalletChanged;
use App\Models\WalletActivity;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\WalletRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WalletBalanceAdjustedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    private $walletRepository;

    public function __construct(WalletRepositoryInterface $walletRepository)
    {
        $this->walletRepository = $walletRepository;
    }

    /**
     * Handle the event.
     *
     * @param  WalletBalanceAdjustedEvent  $event
     * @return void
     */
    public function handle(WalletBalanceAdjustedEvent $event)
    {
        $this->walletRepository->recordActivity($event->wallet->id, $event->amount, $event->fromWallet->id ?? null);

        $userRepository = app(UserRepositoryInterface::class); 
        $user = $userRepository->getUser($event->wallet->user_id);

        Mail::queue(new WalletChanged($user->email, $event->amount, $user->first_name));
    }

    public function failed($event, Exception $e)
    {
        Log::error($e->getMessage(), $e->getTrace());
    }
}
