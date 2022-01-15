<?php

namespace App\Repositories;

use App\Models\LoanOffers;
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
        $data['loan_id'] = $loanId;
        return LoanOffers::create($data);
    }

    public function getOffers($loanId)
    {
        return LoanOffers::where('loan_id', $loanId)->simplePaginate(10);
    }

    public function acceptOffer($loanId)
    {
    }

    public function declineOffer($loanId)
    {
    }
}
