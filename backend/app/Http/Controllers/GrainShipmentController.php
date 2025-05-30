<?php

namespace App\Http\Controllers;

use App\Models\GrainShipment;
use Illuminate\Http\Request;

class GrainShipmentController extends Controller
{
    public function byWarehouse($warehouseId)
    {
        $shipments = GrainShipment::with(['grainType', 'driver'])
            ->where('warehouse_id', $warehouseId)
            ->orderBy('shipment_date', 'desc')
            ->get();

        return response()->json($shipments);
    }

    // Добавь при необходимости методы store/update/delete
}
