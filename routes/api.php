<?php

use Illuminate\Http\Request;
use Dingo\Api\Routing\Router;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['middleware' => 'apiHead'], function (Router $api) {
	$api->group([
		'prefix' => '',
		'middleware' => 'api'
	], function ($api) {
		$api->get('/ping', function() {
			return response()->json('pong',200);
		});
		// $api->post('/generate', function(Request $request) {
		// 	return Uuid::generate()->string;
		// });
	});
	// Authentication Routes 
	$api->group([
	    'namespace' => '\App\Http\Controllers\Auth',
	    'prefix' => 'auth'
	], function ($api) {
		$api->post('login', 'AuthController@login');
		$api->get('/login/{service}', 'SocialLoginController@redirect');
		$api->get('/login/{service}/callback', 'SocialLoginController@callback');
		$api->get('/{service}/test', 'SocialLoginController@test');
		$api->post('signup', 'AuthController@signup');
		$api->post('logout', 'AuthController@logout');
		$api->post('refresh', 'AuthController@refresh');
		$api->post('verify', 'AuthController@verify');
		$api->get('reverify', 'AuthController@reverify');
		$api->post('me', 'AuthController@me');
		$api->post('verified', 'AuthController@verifiedRoute')->middleware('verified');
		$api->group(['middleware'=>[] , 'prefix'=>'mqtt'], function($api) {
		  	$api->post('/verify', 'mqttController@verify');
			$api->post('/admin', 'mqttController@admin');
			$api->get('/acl', 'mqttController@acl');
		});
	});
	// Roles Routes
	$api->group([
		'namespace' => '\App\Http\Controllers\Roles',
		'prefix' => 'roles'
	], function($api) {
		$api->get('/', 'RolesController@index');
		$api->post('/add', 'RolesController@store');
		$api->get('/remove/{id}', 'RolesController@destroy');
		$api->get('/show/{id}', 'RolesController@show');
		$api->post('/grant', 'RolesController@grant');
		$api->post('/revoke', 'RolesController@revoke');
		// $api->post('update/{id}', 'RolesController@update');
	});
	$api->group(['prefix' => 'routes'], function($api) {
		$api->get('admin', function() {
			return response()->json('success',200);
		})->middleware('role:admin');
		$api->get('user', function() {
			return response()->json('success',200);
		})->middleware('apiAuth');
		$api->get('employee', function() {
			return response()->json('success',200);
		})->middleware('role:employee|admin');
	});
});
