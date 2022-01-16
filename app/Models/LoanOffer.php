<?php

namespace App\Models;

use App\Models\Traits\CustomIdentifier;

class LoanOffer extends BaseModel
{
    use CustomIdentifier;

    public $incrementing = false;

    public function request()
    {
        return $this->belongsTo(\App\Models\LoanRequest::class, 'loan_request_id');
    }

    public function accepted(){
        return $this->hasOne(\App\Models\AcceptedOffer::class, 'loan_offer_id');
    }

    public function declined(){
        return $this->hasMany(\App\Models\DeclinedOffer::class, 'loan_offer_id');
    }
}
