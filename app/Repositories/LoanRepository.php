<?php

namespace App\Repositories;

use App\Events\LoanOfferAcceptedEvent;
use App\Models\AcceptedOffer;
use App\Models\DeclinedOffer;
use App\Models\LoanOffer;
use App\Models\LoanRequest;
use App\Repositories\Contracts\LoanRepositoryInterface;
use Illuminate\Http\Request;

class LoanRepository implements LoanRepositoryInterface
{
    public function makeRequest(Request $request, $userId)
    {
        $data = $request->all();
        $data['user_id'] = $userId;
        return LoanRequest::create($data);
    }

    public function getUserRequests($userId)
    {
        return LoanRequest::where('user_id', $userId)->simplePaginate(10);
    }

    public function getAllRequests()
    {
        return LoanRequest::simplePaginate(10);
    }

    public function getRequest($requestId)
    {
        return LoanRequest::findOrFail($requestId);
    }

    public function makeOffer(Request $request, $loanId, $userId)
    {
        $data = $request->all();
        $data['user_id'] = $userId;
        $data['loan_request_id'] = $loanId;
        return LoanOffer::create($data);
    }

    public function getOffers($loanId)
    {
        return LoanOffer::where('loan_request_id', $loanId)->simplePaginate(10);
    }

    public function getOffer($offerId)
    {
        return LoanOffer::with('request')->findOrFail($offerId);
    }

    public function acceptOffer($offerId)
    {

        DeclinedOffer::where('loan_offer_id', $offerId)->delete();
        
        $acceptedOffer =  AcceptedOffer::create([
            'loan_offer_id' => $offerId
        ]);

        event(new LoanOfferAcceptedEvent($this->getOffer($offerId)));
        return $acceptedOffer;
    }

    public function declineOffer($offerId)
    {
        AcceptedOffer::where('loan_offer_id', $offerId)->delete();
        return DeclinedOffer::create([
            'loan_offer_id' => $offerId
        ]);
    }
}
