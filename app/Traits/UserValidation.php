<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait UserValidation
{
    use ApiResponse;

    public function validateUserIsBorrower()
    {
        if(Auth::user()->account_type === User::BORROWER) return true; 
        throw new AuthorizationException("User with lender account type can't perform this action ");
    }

    public function validateUserIsLender()
    {
        if (Auth::user()->account_type === User::LENDER) return true;
        throw new AuthorizationException("User with borrower account type can't perform this action ");
    }
}
