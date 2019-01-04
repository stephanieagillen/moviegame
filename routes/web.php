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


Auth::routes();

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@home');
Route::get('/games-list', 'GameController@list');
Route::post('/games', 'GameController@store');
Route::get('/games/{id}', 'GameController@show');
Route::delete('/games/{id}', 'GameController@destroy');
Route::post('/games/{id}/add-player', 'GameController@addPlayer');
Route::post('/games/{id}/add-movie', 'GameController@addMovie');
Route::post('/calculate/{id}', 'GameController@calculate');
Route::get('/game-list', 'GameController@home');


// Route::get('/games/{id}', 'GameController@game');
// Route::get('/games/show/{id}', 'GameController@show');



