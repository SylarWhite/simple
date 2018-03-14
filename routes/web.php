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