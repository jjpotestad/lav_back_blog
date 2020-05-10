<?php

use Illuminate\Http\Request;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('apitoken')->group(function () {
    // Categories 
    Route::resource('categories','CategoryController')->except('create','edit');
    // Posts
    Route::resource('posts','PostController')->except('create','edit');
    Route::post('posts/upload','PostController@upload')->name('posts.upload');
    // Users
    Route::post('users/upload','UserController@upload')->name('users.upload');
});
Route::get('posts/image/{filename}','PostController@getImage')->name('posts.image');
Route::get('users/avatar/{filename}','UserController@getImage')->name('users.avatar');
Route::resource('users','UserController')->except('create','edit');
Route::post('login','UserController@login')->name('login');