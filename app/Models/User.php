<?php

namespace App\Models;

use App\Models\Traits\CustomIdentifier;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Auth\Authorizable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends BaseModel implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable, CustomIdentifier;

    const BORROWER = 'borrower';
    const LENDER = 'lender';

    public $incrementing = false;

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

    protected $hidden = [
        'password',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function setAccountTypeAttribute($value)
    {
        $this->attributes['account_type'] = strtolower($value);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function wallet(){
        return $this->hasOne(\App\Models\Wallet::class, 'user_id');
    }
}
