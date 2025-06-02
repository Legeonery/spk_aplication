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
            ->where('type', 'out')
            ->orderByDesc('date')
            ->get()
            ->map(function ($usage) {
                return [
                    'id' => $usage->id,
                    'date' => $usage->date,
                    'quantity' => $usage->quantity,
                    'reason' => $usage->reason,
                    'name' => $usage->sparePart->name ?? null,
                    'article' => $usage->sparePart->article ?? null,
                ];
            });

        return response()->json($usages);
    }
}
