<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\WalletRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class WalletController extends Controller
{
    private $walletRepository;

    public function __construct(WalletRepositoryInterface $walletRepository)
    {
        $this->walletRepository = $walletRepository;
    }

    public function index(Request $request)
    {
        try {
            $wallet = $this->walletRepository->get(Auth::id());
            return $this->response(Response::HTTP_OK, __('messages.record-fetched'), $wallet);
        } catch (NotFoundResourceException | ModelNotFoundException $err) {
            return $this->error(Response::HTTP_NOT_FOUND, __('messages.resource-not-found'));
        } catch (Exception $err) {
            Log::error($err->getMessage(), $err->getTrace());
            return $this->serverError();
        }
    }

    public function create(Request $request)
    {
        try {
            $this->validate($request, ['initial_balance' => 'numeric|min:1|max:1000000']);

            $initialBalance = $request->get('initial_balance');

            DB::beginTransaction();

            $wallet = $this->walletRepository->create(Auth::id());

            if ($initialBalance) {
                $wallet = $this->walletRepository->adjustWalletBalance($wallet, $initialBalance);
            }

            DB::commit();

            return $this->response(Response::HTTP_CREATED, __('messages.record-created'), $wallet);
        } catch (ValidationException $err) {
            return $this->validationError($err->errors());
        } catch (Exception $err) {
            DB::rollBack();
            Log::error($err->getMessage(), $err->getTrace());
            return $this->serverError();
        }
    }

    public function topUp(Request $request)
    {
        try {
            $this->validate($request, ['amount' => 'required|min:1|max:1000000']);

            $wallet = $this->walletRepository->create(Auth::id());
            $wallet = $this->walletRepository->adjustWalletBalance($wallet, $request->get('amount'));

            return $this->response(Response::HTTP_CREATED, __('messages.record-updated'), $wallet);
        } catch (ValidationException $err) {
            return $this->validationError($err->errors());
        } catch (Exception $err) {
            Log::error($err->getMessage(), $err->getTrace());
            return $this->serverError();
        }
    }

    public function withdraw(Request $request)
    {
        try {
            $this->validate($request, ['amount' => 'required|min:1|max:1000000']);

            $amount = $request->get('amount');
            $wallet = $this->walletRepository->get(Auth::id());

            if ($amount > $wallet->balance) {
                return $this->validationError("Withdrawal request exceeds wallet balance of " . number_format($wallet->balance, 2));
            }

            $wallet = $this->walletRepository->adjustWalletBalance($wallet, $request->get('amount') * -1);

            return $this->response(Response::HTTP_CREATED, __('messages.record-updated'), $wallet);
        } catch (ValidationException $err) {
            return $this->validationError($err->errors());
        } catch (Exception $err) {
            DB::rollBack();
            Log::error($err->getMessage(), $err->getTrace());
            return $this->serverError();
        }
    }
}
