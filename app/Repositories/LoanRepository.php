<?php

namespace App\Repositories;

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
}
