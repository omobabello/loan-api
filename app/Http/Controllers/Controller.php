<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use ApiResponse;
}
