<?php

namespace App\Http\Controllers;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    
    }

    public function index(){
       return $this->response(200, 'Good start', ['good boy']);
    }

    //
}
