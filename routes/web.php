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
// Route::get('/', 'StaticPagesController@home')->name('home');
// Route::get('/help', 'StaticPagesController@help')->name('help');
// Route::get('/about', 'StaticPagesController@about')->name('about');

use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    $feed_items = [];
    if (Auth::check()) {
        $feed_items = Auth::user()->feed()->paginate(30);
    }
    return view('test1', compact('feed_items'));
});

Route::get('test1', function () {
    $feed_items = [];
    if (Auth::check()) {
        $feed_items = Auth::user()->feed()->paginate(30);
    }
        return view('test1', compact('feed_items'));
});

Route::get('signUp', 'UsersController@create')->name('signUp');

Route::resource('users', 'UsersController');

//login
Route::get('login', 'SessionsController@create')->name('login');
Route::post('login', 'SessionsController@store')->name('login');
Route::delete('logout', 'SessionsController@destroy')->name('logout');

Route::get('signup/confirm/{token}', 'UsersController@confirmEmail')->name('confirm_email');

//密码reset
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

//博客新建和删除
Route::resource('statuses', 'StatusesController', ['only' => ['store', 'destroy']]);

//粉丝和关注路由
Route::get('/users/{user}/followings', 'UsersController@followings')->name('users.followings');
Route::get('/users/{user}/followers', 'UsersController@followers')->name('users.followers');
//新增关注和取消关注
Route::post('/users/followers/{user}', 'FollowersController@store')->name('followers.store');
Route::delete('/users/followers/{user}', 'FollowersController@destroy')->name('followers.destroy');