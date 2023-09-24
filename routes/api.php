<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\DailySaleOverviewController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleReportController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoucherController;
use App\Models\DailySaleOverview;
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
        Route::get('/banned-user', [UserController::class, 'bannedUsers']);
        Route::post('/banned-user', [UserController::class, 'banned']);
        Route::post('/restore-user/{id}', [UserController::class, 'restoreUser']);

        Route::post("/sale_close", [DailySaleOverviewController::class, 'saleClose']);
        // Route::post("/daily_sale_records", [DailySaleOverviewController::class, 'daily']);
        Route::get("/daily_sale_records", [DailySaleOverviewController::class, 'daily']);

        // Route::post("/monthly_sale_record", [DailySaleOverviewController::class, 'monthly']);
        Route::get("/monthly_sale_record", [DailySaleOverviewController::class, 'monthly']);

        // Route::post("/yearly_sale_record", [DailySaleOverviewController::class, 'yearly']);
        Route::get("/yearly_sale_record", [DailySaleOverviewController::class, 'yearly']);

        // Route::post("/custom_sale_records", [DailySaleOverviewController::class, 'customSaleRecords']);
        Route::get("/custom_sale_records", [DailySaleOverviewController::class, 'customSaleRecords']);

        Route::post("register", [AuthController::class, 'register']);


        Route::post('/change-password', [UserController::class, 'passwordChanging']);
        Route::post('/change-staff-password', [UserController::class, 'modifyPassword']);

        // Route::get("devices", [ApiAuthController::class, 'devices']);
        Route::controller(ReportController::class)->group(function () {
            Route::get("instock-product", [ReportController::class, "instockProducts"]);
            Route::get("lowstock-product", [ReportController::class, "getLowStockProducts"]);
            Route::get("outOfStock-product", [ReportController::class, "outOfStockProducts"]);
        });
        Route::controller(SaleReportController::class)->group(function () {
            Route::get("today-sale-report","todaySaleReport");
            // Route::get("weekly-sale","weeklySale");
            // Route::get("get-weekly-sale","getWeeklySales");
            // Route::get("weekly-sale-test","weeklySaleTest");
            // Route::get("weekly-sale-report","weeklySaleReport");
            // Route::get("week","week");
            Route::get("product-sale","productSale");
            Route::get("brand-sale", "brandSale");
            Route::get("sale-overview", "saleOverview");
            // Route::get("sale-overview/{type}", "saleOverview");
            

        });
    });

    Route::post("login", [AuthController::class, 'login']);
});
