<?php

namespace App\Events;

use App\Models\LoanOffer;

class LoanOfferAcceptedEvent extends Event
{
    
    public $loanOffer;

    public function __construct(LoanOffer $loanOffer)
    {
        $this->loanOffer = $loanOffer;
    }
}
