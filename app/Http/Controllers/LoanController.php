<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\LoanRepositoryInterface;
use App\Repositories\Contracts\WalletRepositoryInterface;
use App\Traits\ApiResponse;
use App\Traits\UserValidation;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        } catch (AuthorizationException $e) {
            return $this->error(Response::HTTP_FORBIDDEN, $e->getMessage());
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
        } catch (AuthorizationException $e) {
            return $this->error(Response::HTTP_FORBIDDEN, $e->getMessage());
        } catch (Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return $this->serverError();
        }
    }

    public function getAllRequests(Request $request)
    {
        try {
            $this->validateUserIsLender();
            $loanRequests = $this->loanRepository->getAllRequests(Auth::id());
            return $this->response(Response::HTTP_OK, __('messages.records-fetched'), $loanRequests);
        } catch (AuthorizationException $e) {
            return $this->error(Response::HTTP_FORBIDDEN, $e->getMessage());
        } catch (Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return $this->serverError();
        }
    }

    public function makeOffer(Request $request, $id)
    {
        try {

            $this->validateUserIsLender();

            $loanRequest =  $this->loanRepository->getRequest($id);
            $walletRepository = app(WalletRepositoryInterface::class);
            $lenderWallet = $walletRepository->get(Auth::id());

            $dateAfterOrEqual = date('Y-m-d', strtotime($loanRequest->created_at));

            $this->validate($request, [
                'interest_rate' => 'required|numeric|min:1|max:100',
                'maturity_date' => "required|date|after_or_equal:{$dateAfterOrEqual}|date_format:Y-m-d",
                'terms' => 'required|string'
            ]);

            if ($lenderWallet->balance < $loanRequest->amount) {
                return $this->validationError("Your wallet can't fund this loan");
            }

            $loanOffer = $this->loanRepository->makeOffer($request, $id, Auth::id());

            return $this->response(Response::HTTP_CREATED, __('messages.record-created'), $loanOffer);
        } catch (AuthorizationException $e) {
            return $this->error(Response::HTTP_FORBIDDEN, $e->getMessage());
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

            $loanRequest = $this->loanRepository->getRequestWithOffers($id);

            return $this->response(Response::HTTP_CREATED, __('messages.records-fetched'), $loanRequest);
        } catch (AuthorizationException $e) {
            return $this->error(Response::HTTP_FORBIDDEN, $e->getMessage());
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

            $loanRequest = $loanOffer->request()->first(); 

            Log::info($loanRequest);

            if ($loanOffer->accepted()->count() > 0 || $loanRequest->acceptedOffers()->count() > 0) {
                return $this->error(Response::HTTP_FORBIDDEN, "This request has been previously accepted", $loanOffer->accepted());
            }

            $walletRepository = app(WalletRepositoryInterface::class);
            $lenderWallet = $walletRepository->get($loanOffer->user_id);

            if ($lenderWallet->balance < $loanOffer->request->amount) {
                return $this->error(Response::HTTP_FAILED_DEPENDENCY, "Lender can't fund this loan anymore");
            }

            DB::beginTransaction();

            $this->loanRepository->acceptOffer($offerId);

            DB::commit();

            return $this->response(Response::HTTP_OK, __('messages.record-updated'), $loanOffer);
        } catch (AuthorizationException $e) {
            return $this->error(Response::HTTP_FORBIDDEN, $e->getMessage());
        } catch (NotFoundResourceException | ModelNotFoundException $err) {
            DB::rollBack();
            return $this->error(Response::HTTP_NOT_FOUND, __('messages.resource-not-found'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), $e->getTrace());
            return $this->serverError();
        }
    }

    public function declineOffer(Request $request, $offerId)
    {
        try {

            $this->validateUserIsBorrower();

            $loanOffer = $this->loanRepository->getOffer($offerId);

            if ($loanOffer->accepted()->count() > 0 ) {
                return $this->error(Response::HTTP_FORBIDDEN, "You can not decline an offer you previously accepted", $loanOffer->accepted());
            }

            $this->loanRepository->declineOffer($offerId);

            return $this->response(Response::HTTP_OK, __('messages.record-updated'), $loanOffer);
        } catch (AuthorizationException $e) {
            return $this->error(Response::HTTP_FORBIDDEN, $e->getMessage());
        } catch (NotFoundResourceException | ModelNotFoundException $err) {
            return $this->error(Response::HTTP_NOT_FOUND, __('messages.resource-not-found'));
        } catch (Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return $this->serverError();
        }
    }
}
