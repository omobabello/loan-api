<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface LoanRepositoryInterface
{
    public function makeRequest(Request $request, $userId);
}
