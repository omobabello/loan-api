<?php

namespace App\Models;

use App\Models\Traits\CustomIdentifier;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Auth\Authorizable;

class User extends BaseModel implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, CustomIdentifier;

    const BORROWER = 'borrower';
    const LENDER = 'lender';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'email_confirmed',
        'password',
        'account_type',
        'phone', 
        'address'
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function setAccountTypeAttribute($value)
    {
        $this->attributes['account_type'] = strtolower($value);
    }
}
