<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use App\Models\Wallet;

interface WalletRepositoryInterface
{
    public function create($userId);

    public function get($userId);

    public function topUp(Wallet $wallet, $amount, $fromWallet = null);

    public function debit(Wallet $wallet, $amount, $fromWallet = null);

    public function recordActivity($walletId, $amount, $fromWallet = null);
}
