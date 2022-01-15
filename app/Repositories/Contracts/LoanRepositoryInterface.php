<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface LoanRepositoryInterface
{
    public function makeRequest(Request $request, $userId);

    public function getUserRequests($userId);

    public function getAllRequests();

    public function getRequest($requestId);

    public function makeOffer(Request $request, $loanId, $userId);

    public function getOffers($loanId);

    public function acceptOffer($loanId); 

    public function declineOffer($loanId);
}
