<?php

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// API route group
// $router->group(['prefix' => 'api'], function () use ($router) {
//     $router->post('register', 'AuthController@register'); 
// });

//$router->group(['prefix' => 'api/v1'], function () use ($router) {
//$router->group(['middleware' => 'auth'], function () use ($router){ 
    $router->group(['prefix' => 'api/v1'], function () use ($router) {
        $router->post('register', 'AuthController@register');
        $router->post('logout', 'AuthController@logout');
        $router->post('login', 'AuthController@postLogin');
        $router->post('refresh', 'AuthController@refresh');
        $router->post('user', 'AuthController@getAuthUser');
        
        $router->post('airtime/pay', 'AirtimeController@buyAirtime');
        $router->post('airtime/requery', 'AirtimeController@airtimeQuery');
        $router->post('electricity/merchant-verify', 'ElectricityController@buyElectricity');
        $router->post('electricity/pay', 'ElectricityController@purchaseProduct');
        $router->post('electricity/requery', 'ElectricityController@transactionQuery');
    });
    
    $router->group(['middleware' => 'auth'], function () use ($router){
    });

    //$router->post('/login', 'AuthController@postLogin');


// $router->group(['prefix' => 'api/'], function ($router) {
//     $router->get('login/','UsersController@authenticate');
//     $router->post('todo/','TodoController@store');
//     $router->get('todo/', 'TodoController@index');
//     $router->get('todo/{id}/', 'TodoController@show');
//     $router->put('todo/{id}/', 'TodoController@update');
//     $router->delete('todo/{id}/', 'TodoController@destroy');
// });