<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Middleware\ApiAuthMiddleware;

Route::get('/', function () {
    return view('welcome');
});
//Ruta de prueba del ORM
Route::get('/test-orm', 'PruebaController@prueba');

//Rutas de UserController
Route::post('/register', 'UserController@register');
Route::post('/login', 'UserController@login');
Route::put('/user/update', 'UserController@update')->middleware('api.auth');
Route::post('/user/uploadAvatar', 'UserController@uploadAvatar')->middleware('api.auth');
Route::get('/user/getImage/{filename}', 'UserController@getImage')->middleware('api.auth');
Route::get('/user/detail/{id}', 'UserController@detail')->middleware('api.auth');
//Rutas de CategoryController
Route::resource('/category', 'CategoryController')->middleware('api.auth');
//Rutas de PostController
Route::resource('/post', 'PostController');