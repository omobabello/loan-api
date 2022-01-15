<?php

namespace App\Repositories;

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

    public function topUp(Wallet $wallet, $amount, $fromWallet = null)
    {
        if ($amount > 0) {
            $wallet->balance += $amount;
            $wallet->save();
        }

        //fire up event(s) if required in the future.

        return $wallet;
    }

    public function debit(Wallet $wallet, $amount, $fromWallet = null)
    {
        $amount *= -1;
        if ($amount < 0) {
            $wallet->balance += $amount;
            $wallet->save();
        }

        // fire up event(s) if required in the future.

        return $wallet;
    }

    public function recordActivity($walletId, $amount, $fromWallet = null)
    {
        return WalletActivity::create([
            'wallet_id' => $walletId,
            'amount' => $amount,
            'from-wallet_id' => $fromWallet
        ]);
    }
}
