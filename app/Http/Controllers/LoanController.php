<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\LoanRepositoryInterface;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoanController extends Controller
{
    private $loanRepository;

    public function __construct(LoanRepositoryInterface $loanRepository)
    {
        $this->loanRepository = $loanRepository;
    }

    public function createRequest(Request $request)
    {
        try {
            $this->validate($request, [
                'amount' => 'required|numeric|min:1',
                'proposed_return_date' => 'required|date|after:yesterday|date_format:Y-m-d', //support same day return loan. 
                'monthly_income' => 'required|numeric',
                'employment_status' => 'required|string'
            ]);

            $loanRequest = $this->loanRepository->makeRequest($request, Auth::id());

            return $this->response(Response::HTTP_OK, __('messages.record-created'), $loanRequest);

        } catch (ValidationException $err) {
            return $this->validationError($err->errors());
        } catch (Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return $this->serverError();
        }
    }
}
