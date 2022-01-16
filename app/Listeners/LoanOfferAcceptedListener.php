<?php

namespace App\Listeners;

use App\Events\LoanOfferAcceptedEvent;
use App\Repositories\Contracts\WalletRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoanOfferAcceptedListener
{
    private $walletRepository;

    public function __construct(WalletRepositoryInterface $walletRepository)
    {
        $this->walletRepository = $walletRepository;
    }

    /**
     * Handle the event.
     *
     * @param  LoanOfferAcceptedEvent  $event
     * @return void
     */
    public function handle(LoanOfferAcceptedEvent $event)
    {
        $loanOffer = $event->loanOffer;
        // $loanRequest = $loanOffer->request()->get();

        Log::info($loanOffer); 
        // Log::info($loanRequest);

        Log::info($loanOffer->request->user_id);
        Log::info($loanOffer->user_id);
        
        $borrowerWallet = $this->walletRepository->get($loanOffer->request->user_id);
        $lenderWallet = $this->walletRepository->get($loanOffer->user_id);

        DB::beginTransaction();
        $this->walletRepository->adjustWalletBalance($borrowerWallet, $loanOffer->request->amount, $lenderWallet);
        $this->walletRepository->adjustWalletBalance($lenderWallet, $loanOffer->request->amount * -1, $borrowerWallet);
        DB::commit();
    }

    public function failed($event, Exception $e){
        DB::rollBack(); 
        Log::error($e->getMessage(), $e->getTrace());
    }
}
