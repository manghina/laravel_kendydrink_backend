<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
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
    return response()->json(['success' => 'false'], 401);
})->name('login');



Route::post('login', [RegisterController::class, 'login']);
Route::post('register', [RegisterController::class, 'register']);

Route::group(['middleware' => 'web'], function () {
    
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth');
    
    Route::post('logged', [RegisterController::class, 'getCurrentUser'])
    ->middleware('auth');
    
    Route::get('billing', [BillingController::class, 'all'])->middleware('auth');
    Route::get('billing/{id}', [BillingController::class, 'get'])->middleware('auth');
    Route::post('billing', [BillingController::class, 'create'])->middleware('auth');

    Route::get('products/all', [ProductController::class, 'all']);
    Route::get('product/category/{id}', [ProductController::class, 'findByCategory']);
    Route::get('product/best', [ProductController::class, 'best']);
    Route::get('product/{id}', [ProductController::class, 'findById']);
    Route::get('product/{id}/img/{resolution}', [ProductController::class, 'findImgById']);
    Route::get('product/{id}/img', [ProductController::class, 'findImgById']);
    Route::post('test', [CardController::class, 'test']);

    Route::post('checkout', [OrderController::class, 'checkout'])
    ->middleware('auth');

    Route::put('customers', [CustomerController::class, 'create']); // da togliere
    Route::post('customers', [CustomerController::class, 'update']); // da togliere
    Route::get('customers/{var1}', [CustomerController::class, 'get']); // da togliere
    Route::delete('customers/{var1}', [CustomerController::class, 'delete']); // da togliere

    Route::post('card', [CardController::class, 'create'])
    ->middleware('auth');
});
Route::get('send-mail', [MailController::class, 'index']);