<?php

namespace App\Http\Controllers;

use App\Models\GrainDelivery;
use Illuminate\Http\Request;

class GrainDeliveryController extends Controller
{
    // Создание новой поставки
    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'grain_type_id' => 'required|exists:grain_types,id',
            'volume' => 'required|numeric|min:0.1',
            'delivery_date' => 'required|date',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
        ]);

        $delivery = GrainDelivery::create($validated);

        return response()->json(['message' => 'Поставка добавлена', 'data' => $delivery]);
    }

    // Получение поставок по складу
    public function byWarehouse($warehouseId)
    {
        try {
            $deliveries = GrainDelivery::with(['grainType', 'driver'])
                ->where('warehouse_id', $warehouseId)
                ->orderBy('delivery_date', 'desc')
                ->get();
            return response()->json($deliveries);
        } catch (\Exception $e) {
            \Log::error('Ошибка при загрузке поставок: ' . $e->getMessage());
            return response()->json(['message' => 'Ошибка сервера'], 500);
        }
    }
}
