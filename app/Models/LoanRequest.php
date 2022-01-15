<?php

namespace App\Models;

use App\Models\Traits\CustomIdentifier;

class LoanRequest extends BaseModel
{
    use CustomIdentifier;

    public $incrementing = false;
}
