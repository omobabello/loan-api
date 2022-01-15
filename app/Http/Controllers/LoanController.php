<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\LoanRepositoryInterface;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    private $loanRepository;

    public function __construct(LoanRepositoryInterface $loanRepository)
    {
        $this->loanRepository = $loanRepository;
    }

    public function request(Request $request){
        
    }
}
