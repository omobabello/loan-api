<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

// $router->get('/', function () use ($router) {
//     return $router->app->version();
// });

$router->get('/', 'ExampleController@index');

$router->group(['prefix' => 'api'], function () use ($router) {

    $router->group(['middleware' => 'throttle:5'], function () use ($router) {
        $router->get('login', 'UserController@login');
        $router->post('register', 'UserController@register');
        $router->get('users/{id}/confirm/{hash}', 'UserController@confirm');
    });

    $router->group(['middleware' => ['throttle:1,1', 'auth']], function () use ($router) {
        $router->get('refresh', 'UserController@refresh');
        $router->get('logout', 'UserController@logout');
    });

    $router->group(['middleware' => ['throttle:10,1', 'auth']], function () use ($router) {
        $router->post('wallets', 'WalletController@create');
        $router->post('wallets/topup', 'WalletController@topup');
        $router->post('wallets/withdraw', 'WalletController@withdraw');
        $router->get('wallets', 'WalletController@index'); 
        $router->get('wallets/activities', 'WalletController@activities');

        $router->post('loans/requests', 'LoanController@createRequest');
        $router->get('loans/requests', 'LoanController@getUserRequests');
        $router->get('loans/requests/all', 'LoanController@getAllRequests');
    });
});
