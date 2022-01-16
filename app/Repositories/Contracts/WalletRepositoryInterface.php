<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use App\Models\Wallet;

interface WalletRepositoryInterface
{
    public function create($userId);

    public function get($userId);

    public function adjustWalletBalance(Wallet $wallet, $amount, Wallet $fromWallet = null);

    public function recordActivity($walletId, $amount, $fromWalletId = null);
}
