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

require_once '../app/API/SWAPI.php';

use App\API\SWAPI;

//default route
$router->get('/', function () use ($router) {
	return $router->app->version();
});

//api route
$router->get('/SWAPI/people/{page}', function ($page) {
	$swapi = new SWAPI;
	return $swapi->getPeople($page);
});