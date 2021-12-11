<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

// Habilita a utilização de Rotas com o Middleware Auth
Auth::routes();

Route::redirect('/', '/dashboard');

Route::get('/login', 'App\Http\Controllers\Auth\LoginController@index')->name('login');
Route::get('/auth/discord/redirect', 'App\Http\Controllers\Auth\LoginController@discord')->name('login.discord');
Route::get('/auth/callback', 'App\Http\Controllers\Auth\LoginController@discordCallback');

// Grupo de Middleware Auth
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', 'App\Http\Controllers\DashboardController@index')->name('dashboard');
    Route::get('/logout', 'App\Http\Controllers\Auth\LoginController@logout')->name('logout');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
