<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\WarehousesController;
use App\Http\Controllers\GrainTypesController;
use App\Http\Controllers\GrainDeliveryController;
use App\Http\Controllers\GrainShipmentController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\GrainController;
use App\Http\Controllers\WarehouseReportController;
use App\Http\Controllers\SparePartController;
use App\Http\Controllers\SparePartMovementController;
use App\Http\Controllers\SparePartDeliveryController;
use App\Http\Controllers\SparePartUsageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});

Route::resource('posts', PostController::class)->middleware('jwt.auth');

Route::resource('warehouses', WarehousesController::class)->middleware('jwt.auth');


Route::middleware('jwt.auth')->group(function () {
    Route::post('/grain-shipments', [GrainShipmentController::class, 'store']);
    Route::get('/warehouses/{warehouse}/grains', [WarehousesController::class, 'grains']);
    Route::post('/grain-deliveries', [GrainDeliveryController::class, 'store']);
    Route::get('/grain-types', [GrainTypesController::class, 'index']);
    Route::get('/warehouses/{warehouse}/deliveries', [GrainDeliveryController::class, 'byWarehouse']);
    Route::get('/warehouses/{warehouse}/shipments', [GrainShipmentController::class, 'byWarehouse']);
    Route::get('/drivers', [DriverController::class, 'index']);
    Route::get('/vehicles', [VehicleController::class, 'index']);
    Route::put('/grain-deliveries/{id}', [GrainDeliveryController::class, 'update']);
    Route::put('/grain-shipments/{id}', [GrainShipmentController::class, 'update']);
    Route::get('/warehouses/{warehouse}/grains', [GrainController::class, 'getWarehouseGrains']);
    Route::get('/warehouses/{id}/report', [WarehouseReportController::class, 'generate']);
    Route::get('/spare-parts', [SparePartController::class, 'index']);
    Route::post('/spare-parts', [SparePartController::class, 'store']);
    Route::put('/spare-parts/{sparePart}', [SparePartController::class, 'update']);
    Route::delete('/spare-parts/{sparePart}', [SparePartController::class, 'destroy']);

    Route::get('/warehouses/{warehouse}/spare-parts', [SparePartMovementController::class, 'index']);
    Route::post('/warehouses/{warehouse}/spare-parts/deliveries', function ($warehouseId, Request $request) {
        return app(SparePartMovementController::class)->store($request->merge(['type' => 'in']), $warehouseId);
    });
    Route::post('/warehouses/{warehouse}/spare-parts/usages', function ($warehouseId, Request $request) {
        return app(SparePartMovementController::class)->store($request->merge(['type' => 'out']), $warehouseId);
    });
    Route::get('/warehouses/{warehouse}/spare-parts/deliveries', [SparePartDeliveryController::class, 'index']);
    Route::get('/warehouses/{warehouse}/spare-parts/usages', [SparePartUsageController::class, 'index']);
    Route::get('/warehouses/{warehouse}/spare-parts', [SparePartMovementController::class, 'getStock']);
});
