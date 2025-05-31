<?php

namespace App\Http\Controllers;

use App\Models\WarehouseGrain;

class GrainController extends Controller
{
    /**
     * Получить остатки зерна на складе по его ID.
     *
     * @param int $warehouseId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWarehouseGrains($warehouseId)
    {
        $grains = WarehouseGrain::with('grainType')
            ->where('warehouse_id', $warehouseId)
            ->get();

        return response()->json($grains);
    }
}
