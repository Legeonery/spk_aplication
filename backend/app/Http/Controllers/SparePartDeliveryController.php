<?php

namespace App\Http\Controllers;

use App\Models\SparePartMovement;
use Illuminate\Http\Request;

class SparePartDeliveryController extends Controller
{
    /**
     * Получение истории поступлений запчастей для склада
     *
     * @param int $warehouseId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($warehouseId)
    {
        $deliveries = SparePartMovement::with('sparePart')
            ->where('warehouse_id', $warehouseId)
            ->where('type', 'in') // Поступление
            ->orderByDesc('date')
            ->get();

        return response()->json($deliveries);
    }
}
