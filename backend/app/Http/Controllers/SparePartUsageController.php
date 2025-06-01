<?php

namespace App\Http\Controllers;

use App\Models\SparePartMovement;
use Illuminate\Http\Request;

class SparePartUsageController extends Controller
{
    /**
     * Получение истории списаний запчастей для склада
     *
     * @param int $warehouseId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($warehouseId)
    {
        $usages = SparePartMovement::with('sparePart')
            ->where('warehouse_id', $warehouseId)
            ->where('type', 'out') // Списание
            ->orderByDesc('date')
            ->get();

        return response()->json($usages);
    }
}
