<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ManufacturerController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\DeliveriesController;
use App\Http\Controllers\Api\PurchasesController;
use App\Http\Controllers\Api\StatisticsController;
use App\Http\Controllers\Api\UserController;

use \App\Http\Controllers\Api\AuthApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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



//Route::get('/get_user', [CategoryController::class, 'get_user']);



Route::post('/register', [AuthApi::class, 'register']);
Route::post('/login', [AuthApi::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function (){
    Route::get('/user', [AuthApi::class, 'getUser']);

    Route::post('/profile', [ ProfileController::class, 'update']);

    Route::get('/categories/{page?}', [CategoryController::class, 'index']);
    Route::get('/category/{id}', [CategoryController::class, 'show']);
    Route::post('/category', [CategoryController::class, 'store']);
    Route::put('/category/{id}', [CategoryController::class, 'update']);
    Route::delete('/category/{id}', [CategoryController::class, 'destroy']);

    Route::get('/customers/{page?}', [CustomerController::class, 'index']);
    Route::get('/customer/{id}', [CustomerController::class, 'show']);
    Route::post('/customer', [CustomerController::class, 'store']);
    Route::put('/customer/{id}', [CustomerController::class, 'update']);
    Route::delete('/customer/{id}', [CustomerController::class, 'destroy']);
    Route::get('/customers-find', [CustomerController::class, 'customers_find']);

    Route::get('/manufacturers/{page?}', [ManufacturerController::class, 'index']);
    Route::get('/manufacturer/{id}', [ManufacturerController::class, 'show']);
    Route::post('/manufacturer', [ManufacturerController::class, 'store']);
    Route::put('/manufacturer/{id}', [ManufacturerController::class, 'update']);
    Route::delete('/manufacturer/{id}', [ManufacturerController::class, 'destroy']);

    Route::get('/products/{page?}', [ProductController::class, 'index']);
    Route::get('/product/{id}', [ProductController::class, 'show']);
    Route::post('/product', [ProductController::class, 'store']);
    Route::post('/product/{id}', [ProductController::class, 'update']);
    Route::delete('/product/{id}', [ProductController::class, 'destroy']);
    Route::get('/product-find', [ProductController::class, 'products_find']);

    Route::get('/stores/{page?}', [StoreController::class, 'index']);
    Route::get('/store/{id}', [StoreController::class, 'show']);
    Route::post('/store', [StoreController::class, 'store']);
    Route::put('/store/{id}', [StoreController::class, 'update']);
    Route::delete('/store/{id}', [StoreController::class, 'destroy']);

    Route::get('/statistics/basic', [StatisticsController::class, 'basic']);

    Route::get('/deliveries/chart', [DeliveriesController::class, 'chart']);
    Route::get('/deliveries/{page?}', [DeliveriesController::class, 'index']);
    Route::get('/delivery/{id}', [DeliveriesController::class, 'show']);
    Route::post('/delivery', [DeliveriesController::class, 'store']);
    Route::put('/delivery/{id}', [DeliveriesController::class, 'update']);
    Route::delete('/delivery/{id}', [DeliveriesController::class, 'destroy']);

    Route::get('/purchases/chart', [PurchasesController::class, 'chart']);
    Route::get('/purchases/{page?}', [PurchasesController::class, 'index']);
    Route::get('/purchase/{id}', [PurchasesController::class, 'show']);
    Route::post('/purchase', [PurchasesController::class, 'store']);
    Route::put('/purchase/{id}', [PurchasesController::class, 'update']);
    Route::delete('/purchase/{id}', [PurchasesController::class, 'destroy']);



    Route::middleware(['admin'])->group(function (){
        Route::get('/users/{page?}', [UserController::class, 'index']);
        Route::get('/user/{id}', [UserController::class, 'show']);
        Route::post('/user', [UserController::class, 'store']);
        Route::post('/user/{id}', [UserController::class, 'update']);
        Route::delete('/user/{id}', [UserController::class, 'destroy']);
    });

    /*
    Route::get('/user', function (Request $request) {
        return auth()->user();
    });
    */

    Route::post('/logout', [AuthApi::class, 'logout']);
});

/*
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
*/

//require __DIR__.'/auth.php';




Route::any('{path}', function() {
    return response()->json([
        'success' => false,
        'errors' => ['404 not found']
    ], 404);
})->where('path', '.*');


