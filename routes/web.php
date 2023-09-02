<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\RegisterController;

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
    return view('login');
})->name('login');

Route::group(['middleware' => 'web'], function () {
    Route::post('test', [RegisterController::class, 'getCurrentUser']);
    Route::post('register', [RegisterController::class, 'register']);
    Route::any('login', [RegisterController::class, 'login'])                
    ->middleware('guest')
    ->name('login');
    Route::get('/products/all', [ProductController::class, 'all']);
    Route::get('/product/category/{id}', [ProductController::class, 'findByCategory']);
    Route::get('/product/best', [ProductController::class, 'best']);
    Route::get('/product/{id}', [ProductController::class, 'findById']);
    Route::get('/product/{id}/img/{resolution}', [ProductController::class, 'findImgById']);
    Route::get('/product/{id}/img', [ProductController::class, 'findImgById']);
    Route::post('/orders/create', [OrderController::class, 'test']);
    Route::post('/orders/checkout', [OrderController::class, 'checkout']);
    Route::put('/customers', [CustomerController::class, 'create']);
    Route::post('/customers', [CustomerController::class, 'update']);
    Route::get('/customers/{var1}', [CustomerController::class, 'get']);
    Route::delete('/customers/{var1}', [CustomerController::class, 'delete']);
    Route::post('/card', [CardController::class, 'create']);
});
