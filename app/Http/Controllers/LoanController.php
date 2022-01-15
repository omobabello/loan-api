<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\LoanRepositoryInterface;
use App\Traits\ApiResponse;
use App\Traits\UserValidation;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class LoanController extends Controller
{
    use ApiResponse, UserValidation;

    private $loanRepository;

    public function __construct(LoanRepositoryInterface $loanRepository)
    {
        $this->loanRepository = $loanRepository;
    }

    public function makeRequest(Request $request)
    {
        try {
            $this->validateUserIsBorrower();

            $this->validate($request, [
                'amount' => 'required|numeric|min:1',
                'proposed_return_date' => 'required|date|after:yesterday|date_format:Y-m-d', //support same day return loan. 
                'monthly_income' => 'required|numeric',
                'employment_status' => 'required|string'
            ]);

            $loanRequest = $this->loanRepository->makeRequest($request, Auth::id());

            return $this->response(Response::HTTP_CREATED, __('messages.record-created'), $loanRequest);
        } catch (ValidationException $err) {
            return $this->validationError($err->errors());
        } catch (Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return $this->serverError();
        }
    }

    public function getUserRequests(Request $request)
    {
        try {
            $this->validateUserIsBorrower();

            $loanRequests = $this->loanRepository->getUserRequests(Auth::id());
            return $this->response(Response::HTTP_OK, __('messages.records-fetched'), $loanRequests);
        } catch (ValidationException $err) {
            return $this->validationError($err->errors());
        } catch (Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return $this->serverError();
        }
    }

    public function getAllRequests(Request $request)
    {
        try {
            $loanRequests = $this->loanRepository->getAllRequests(Auth::id());
            return $this->response(Response::HTTP_OK, __('messages.records-fetched'), $loanRequests);
        } catch (Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return $this->serverError();
        }
    }

    public function makeOffer(Request $request, $id)
    {
        try {

            $this->validateUserIsLender();

            $this->loanRepository->getRequest($id);

            $this->validate($request, [
                'interest_rate' => 'required|numeric|min:1|max:100',
                'maturity_date' => "required|date|date_format:Y-m-d",
                'terms' => 'required|string'
            ]);

            $loanOffer = $this->loanRepository->makeOffer($request, $id, Auth::id());

            return $this->response(Response::HTTP_CREATED, __('messages.record-created'), $loanOffer);
        } catch (ValidationException $err) {
            return $this->validationError($err->errors());
        } catch (NotFoundResourceException | ModelNotFoundException $err) {
            return $this->error(Response::HTTP_NOT_FOUND, __('messages.resource-not-found'));
        } catch (Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return $this->serverError();
        }
    }

    public function getOffers(Request $request, $id)
    {
        try {

            $this->validateUserIsBorrower();

            $loanRequest = $this->loanRepository->getRequest($id);

            $loanOffer = $this->loanRepository->getOffers($id);

            return $this->response(
                Response::HTTP_CREATED,
                __('messages.records-fetched'),
                ['request' => $loanRequest, 'offers' =>  $loanOffer]
            );
        } catch (ValidationException $err) {
            return $this->validationError($err->errors());
        } catch (NotFoundResourceException | ModelNotFoundException $err) {
            return $this->error(Response::HTTP_NOT_FOUND, __('messages.resource-not-found'));
        } catch (Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return $this->serverError();
        }
    }

    public function acceptOffer(Request $request, $offerId)
    {
        try {

            $this->validateUserIsBorrower();

            $loanOffer = $this->loanRepository->getOffer($offerId);

            $this->loanRepository->acceptOffer($offerId);

            return $this->response(Response::HTTP_OK, __('messages.record-updated'), $loanOffer);
        } catch (ValidationException $err) {
            return $this->validationError($err->errors());
        } catch (NotFoundResourceException | ModelNotFoundException $err) {
            return $this->error(Response::HTTP_NOT_FOUND, __('messages.resource-not-found'));
        } catch (Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return $this->serverError();
        }
    }

    public function declineOffer(Request $request, $offerId)
    {
        try {

            $this->validateUserIsBorrower();

            $loanOffer = $this->loanRepository->getOffer($offerId);

            $this->loanRepository->declineOffer($offerId);

            return $this->response(Response::HTTP_OK, __('messages.record-updated'), $loanOffer);
        } catch (ValidationException $err) {
            return $this->validationError($err->errors());
        } catch (NotFoundResourceException | ModelNotFoundException $err) {
            return $this->error(Response::HTTP_NOT_FOUND, __('messages.resource-not-found'));
        } catch (Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return $this->serverError();
        }
    }
}
