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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', 'StaticPagesController@home')->name('home');
Route::get('/faq', 'StaticPagesController@help')->name('help');
Route::get('/about', 'StaticPagesController@about')->name('about');

# 打开注册页面
Route::get('signup', 'UserController@create')->name('signup');

// 用户增删改查
Route::resource('users', 'UserController');

// 显示登录页面
Route::get('login', 'SessionController@create')->name('login');

// 	创建新会话（登录）
Route::post('login', 'SessionController@store')->name('login');

// 销毁会话（退出登录）
Route::delete('logout', 'SessionController@destroy')->name('logout');

// 邮箱注册验证
Route::get('signup/confirm/{token}', 'UserController@confirmEmail')->name('confirm_email');

// 重置密码
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

// 微博创建和删除
Route::resource('statuses', 'StatusController', ['only'=>['store', 'destroy']]);

//关注列表和粉丝列表
Route::get('/users/{user}/followings','UserController@followings')->name('users.followings');
Route::get('/users/{user}/followers','UserController@followers')->name('users.followers');

//关注和取消关注
Route::post('/users/followers/{user}','FollowersController@store')->name('followers.store');
Route::delete('/users/followers/{user}','FollowersController@destroy')->name('followers.destroy');
