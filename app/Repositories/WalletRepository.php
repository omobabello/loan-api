<?php

namespace App\Repositories;

use App\Events\WalletBalanceAdjustedEvent;
use App\Models\Wallet;
use App\Models\WalletActivity;
use App\Repositories\Contracts\WalletRepositoryInterface;

class WalletRepository implements WalletRepositoryInterface
{
    public function create($userId)
    {
        return Wallet::firstOrCreate(['user_id' => $userId]);
    }

    public function get($userId)
    {
        return Wallet::where('user_id', $userId)->firstOrFail();
    }

    public function adjustWalletBalance(Wallet $wallet, $amount, Wallet $fromWallet = null)
    {
        $wallet->balance += $amount;
        $wallet->save();

        event(new WalletBalanceAdjustedEvent($wallet, $amount, $fromWallet));
        return $wallet;
    }

    public function recordActivity($walletId, $amount, $fromWalletId = null)
    {
        return WalletActivity::create([
            'wallet_id' => $walletId,
            'amount' => $amount,
            'from_wallet_id' => $fromWalletId
        ]);
    }
}
