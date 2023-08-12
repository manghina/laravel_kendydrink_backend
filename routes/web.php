<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/products/all', [ProductController::class, 'all']);
Route::get('/product/category/{id}', [ProductController::class, 'findByCategory']);
Route::get('/product/best', [ProductController::class, 'best']);
Route::get('/product/{id}', [ProductController::class, 'findById']);
Route::get('/product/{id}/img/{resolution}', [ProductController::class, 'findImgById']);
Route::get('/product/{id}/img', [ProductController::class, 'findImgById']);
Route::post('/orders/checkout', [OrderController::class, 'checkout']);
Route::put('/customers/create', [CustomerController::class, 'create']);
Route::get('/token', function () {
    return csrf_token(); 
});