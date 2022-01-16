<?php

namespace App\Listeners;

use App\Events\WalletBalanceAdjustedEvent;
use App\Models\WalletActivity;
use App\Repositories\Contracts\WalletRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;

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
    }

    public function failed($event, Exception $e)
    {
        Log::error($e->getMessage(), $e->getTrace());
    }
}
