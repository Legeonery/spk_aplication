<?php

namespace App\Http\Controllers;

use App\Models\GrainLog;
use App\Models\GrainDelivery;
use App\Models\WarehouseGrain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        WarehouseGrain::updateOrCreate(
            [
                'warehouse_id' => $delivery->warehouse_id,
                'grain_type_id' => $delivery->grain_type_id,
            ],
            []
        )->increment('amount', $delivery->volume);

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
            Log::error('Ошибка при загрузке поставок: ' . $e->getMessage());
            return response()->json(['message' => 'Ошибка сервера'], 500);
        }
    }
    public function update(Request $request, $id)
    {
        $record = GrainDelivery::findOrFail($id);

        $old = $record->only([
            'grain_type_id',
            'volume',
            'delivery_date',
            'vehicle_id',
            'driver_id'
        ]);
        $oldWarehouseId = $record->warehouse_id;

        $validated = $request->validate([
            'grain_type_id' => 'required|exists:grain_types,id',
            'volume' => 'required|numeric|min:0.1',
            'delivery_date' => 'required|date',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
        ]);

        $record->update($validated);
        $new = $record->only([
            'grain_type_id',
            'volume',
            'delivery_date',
            'vehicle_id',
            'driver_id'
        ]);

        // === Корректировка остатков ===
        if ($old['grain_type_id'] == $new['grain_type_id']) {
            $diff = $new['volume'] - $old['volume'];
            $grain = WarehouseGrain::firstOrCreate(
                ['warehouse_id' => $oldWarehouseId, 'grain_type_id' => $new['grain_type_id']],
                ['amount' => 0]
            );
            $grain->amount += $diff;
            $grain->amount = max($grain->amount, 0); 
            $grain->save();
        } else {
            // Старая культура — вычитаем
            $oldGrain = WarehouseGrain::firstOrCreate(
                ['warehouse_id' => $oldWarehouseId, 'grain_type_id' => $old['grain_type_id']],
                ['amount' => 0]
            );
            $oldGrain->amount -= $old['volume'];
            $oldGrain->amount = max($oldGrain->amount, 0); 
            $oldGrain->save();

            // Новая культура — прибавляем
            $newGrain = WarehouseGrain::firstOrCreate(
                ['warehouse_id' => $oldWarehouseId, 'grain_type_id' => $new['grain_type_id']],
                ['amount' => 0]
            );
            $newGrain->amount += $new['volume'];
            $newGrain->save();
        }

        // === Логирование ===
        $changes = [];
        foreach (array_keys($old) as $key) {
            if ($old[$key] != $new[$key]) {
                $changes[$key] = [$old[$key], $new[$key]];
            }
        }

        if (!empty($changes)) {
            GrainLog::create([
                'entity_type' => 'delivery',
                'entity_id' => $record->id,
                'changes' => $changes,
                'action' => 'updated',
            ]);
        }

        return response()->json(['data' => $record]);
    }
}
