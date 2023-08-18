<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoucherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix("v1")->group(function () {

    Route::middleware('auth:sanctum')->group(function () {

        Route::apiResource("brand", BrandController::class);
        Route::apiResource("product", ProductController::class);
        Route::apiResource("stock", StockController::class);
        Route::apiResource("voucher", VoucherController::class);

        Route::apiResource("photo", PhotoController::class);
        Route::apiResource("user", UserController::class);

        Route::post("logout", [AuthController::class, 'logout']);
        Route::post("logout-all", [AuthController::class, 'logoutAll']);
        Route::post('/ban-user/{id}', [UserController::class, 'ban']);

        Route::post("register", [AuthController::class, 'register']);


        Route::post('/change-password', [UserController::class, 'passwordChanging']);
        Route::post('/change-staff-password', [UserController::class, 'modifyPassword']);

        // Route::get("devices", [ApiAuthController::class, 'devices']);

    });

    Route::post("login", [AuthController::class, 'login']);
});
