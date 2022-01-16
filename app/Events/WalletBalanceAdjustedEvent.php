<?php

namespace App\Events;

use App\Models\Wallet;
use Illuminate\Support\Facades\Log;

class WalletBalanceAdjustedEvent extends Event
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $wallet;
    public $amount;
    public $fromWallet;


    public function __construct(Wallet $wallet, $amount, $fromWallet)
    {
        $this->wallet = $wallet;
        $this->amount = $amount;
        $this->fromWallet = $fromWallet;
    }
}
