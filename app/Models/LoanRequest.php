<?php

namespace App\Models;

use App\Models\Traits\CustomIdentifier;

class LoanRequest extends BaseModel
{
    use CustomIdentifier;

    public $incrementing = false;

    public function offers()
    {
        return $this->hasMany(\App\Models\LoanOffer::class, 'loan_request_id');
    }

    public function acceptedOffers()
    {
        return $this->hasManyThrough(
            \App\Models\AcceptedOffer::class,
            \App\Models\LoanOffer::class,
            'loan_request_id',
            'loan_offer_id',
        );
    }

    public function declinedOffers()
    {
        return $this->hasManyThrough(
            \App\Models\DeclinedOffer::class,
            \App\Models\LoanOffer::class,
            'loan_request_id',
            'loan_offer_id'
        );
    }
}
