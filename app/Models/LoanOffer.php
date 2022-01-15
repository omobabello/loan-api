<?php

namespace App\Models;

use App\Models\Traits\CustomIdentifier;

class LoanOffer extends BaseModel
{
    use CustomIdentifier;

    public function request()
    {
        return $this->belongsTo(\App\Models\LoanRequest::class, 'loan_id');
    }
}
