<?php

namespace App\Models;

use App\Traits\ApiResponse;

class LoanRequest extends BaseModel
{
    use ApiResponse; 

    public $incrementing = false;
}
