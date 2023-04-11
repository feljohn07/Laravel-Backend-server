<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\SupplierController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('login', [UserController::class, 'login']);
Route::post('signup', [UserController::class, 'register']);

Route::get('customer', [CustomerController::class, 'index']);
Route::post('customer', [CustomerController::class, 'store']);
Route::get('customer/{id}', [CustomerController::class, 'show']);
Route::put('customer/{id}', [CustomerController::class, 'update']);
Route::delete('customer/{id}', [CustomerController::class, 'destroy']);

Route::get('supplier', [SupplierController::class, 'index']);
Route::post('supplier', [SupplierController::class, 'store']);
Route::get('supplier/{id}', [SupplierController::class, 'show']);
Route::put('supplier/{id}', [SupplierController::class, 'update']);
Route::delete('supplier/{id}', [SupplierController::class, 'destroy']);
