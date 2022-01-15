<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait UserValidation
{

    public function validateUserIsBorrower()
    {
        if(Auth::user()->account_type === User::BORROWER) return true; 
        throw ValidationException::withMessages(["User with lender account type can't perform this action "]);
    }

    public function validateUserIsLender()
    {
        if (Auth::user()->account_type === User::LENDER) return true;
        throw ValidationException::withMessages(["User with borrow account type can't perform this action"]);
    }
}
