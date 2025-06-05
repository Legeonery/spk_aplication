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
use App\Http\Controllers\SparePartReportController;
use App\Http\Controllers\VehicleKindController;
use App\Http\Controllers\TareMeasurementController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\LicenseCategoryController;
use App\Http\Controllers\SparePartRequestController;
use App\Http\Controllers\VehicleRepairController;
use App\Models\Vehicle;
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

    Route::get('/warehouses/{warehouse}/spare-parts/history', [SparePartMovementController::class, 'index']);
    Route::post('/warehouses/{warehouse}/spare-parts/deliveries', function ($warehouseId, Request $request) {
        return app(SparePartMovementController::class)->store($request->merge(['type' => 'in']), $warehouseId);
    });
    Route::post('/warehouses/{warehouse}/spare-parts/usages', function ($warehouseId, Request $request) {
        return app(SparePartMovementController::class)->store($request->merge(['type' => 'out']), $warehouseId);
    });
    Route::get('/warehouses/{warehouse}/spare-parts/deliveries', [SparePartDeliveryController::class, 'index']);
    Route::get('/warehouses/{warehouse}/spare-parts/usages', [SparePartUsageController::class, 'index']);
    Route::get('/warehouses/{warehouse}/spare-parts', [SparePartMovementController::class, 'getStock']);
    Route::get('/warehouses/{warehouse}/spare-parts/report', [SparePartReportController::class, 'generate']);
    Route::apiResource('vehicles', VehicleController::class);
    Route::apiResource('vehicle-kinds', VehicleKindController::class);
    Route::post('/tare-measurements/check', [TareMeasurementController::class, 'needsMeasurement']);
    Route::post('/tare-measurements', [TareMeasurementController::class, 'store']);
    Route::get('/tare-measurements/check', [TareMeasurementController::class, 'check']);
    Route::patch('/vehicles/{vehicle}/toggle-availability', [VehicleController::class, 'toggleAvailability']);
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::get('/roles', [RoleController::class, 'index']);
    Route::get('/license-categories', [LicenseCategoryController::class, 'index']);
    Route::apiResource('grain-types', GrainTypesController::class);
    Route::get('/repairs', [VehicleRepairController::class, 'index']);
    Route::post('/repairs', [VehicleRepairController::class, 'store']);
    Route::delete('/repairs/{vehicleRepair}', [VehicleRepairController::class, 'destroy']);
    Route::get('/spare-requests', [SparePartRequestController::class, 'index']);
    Route::post('/spare-requests', [SparePartRequestController::class, 'store']);
    Route::patch('/spare-requests/{sparePartRequest}/status', [SparePartRequestController::class, 'updateStatus']);
    Route::delete('/spare-requests/{sparePartRequest}', [SparePartRequestController::class, 'destroy']);
    Route::get('/spare-parts/available', [SparePartController::class, 'available']);
    Route::get('/my-vehicles', function () {
        return Vehicle::where('driver_id', Auth::id())->get();
    });
    Route::patch('/vehicles/{vehicle}/status', [VehicleController::class, 'updateStatus']);
});
