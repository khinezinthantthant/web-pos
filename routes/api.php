<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
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

        Route::post('/test', [StockController::class,'test']);
 
        Route::controller(UserController::class)->group(function () {
            Route::apiResource("user", UserController::class);
            Route::post('/ban-user/{id}', 'banUser');
            Route::get('/ban-user-list', 'bannedUsers');
            Route::post('/restore-user/{id}', 'restoreUser');
            Route::post('/change-password', 'passwordChanging');
            Route::post('/change-staff-password', 'modifyPassword');
        });

        Route::controller(AuthController::class)->group(function () {
            Route::post("logout", 'logout');
            Route::post("logout-all", 'logoutAll');
        });

        Route::controller(FinanceController::class)->group(function () {
            Route::post("/sale_close", 'saleClose');
            Route::post("/sale_open", 'saleOpen');
            Route::get("/daily_sale_records", 'daily');
            Route::get("/monthly_sale_record", 'monthly');
            Route::get("/yearly_sale_record", 'yearly');
            Route::get("/custom_sale_records", 'customSaleRecords');
            Route::get("/year", 'year');
        });


        Route::controller(ReportController::class)->group(function () {
            Route::get("/stock_report", 'stockReport');
            Route::get("/brand_report", 'brandReport');
            Route::get("/today-sale-report", 'todaySaleReport');
            Route::get("/product-sale-report", 'productSaleReport');
            Route::get("/brand-sale-report", 'brandSaleReport');
            Route::get("/weekely-sale-report", 'weeklySaleReport');
            Route::get("/monthly-sale-report", 'monthlySaleReport');
            Route::get("/yearly-sale-report", 'yearlySaleReport');
            Route::get("/weekely_best_seller_brands", 'weekelyBestSellerBrands');
            Route::get('/overview', 'overview');

        });

        // Route::post("register", [AuthController::class, 'register']);
        // Route::get("devices", [ApiAuthController::class, 'devices']);

    });

    Route::post("login", [AuthController::class, 'login']);

});
