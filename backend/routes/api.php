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

});
