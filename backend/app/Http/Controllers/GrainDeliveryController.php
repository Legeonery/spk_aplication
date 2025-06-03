<?php

namespace App\Http\Controllers;

use App\Models\GrainLog;
use App\Models\GrainDelivery;
use App\Models\WarehouseGrain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Vehicle;
use App\Models\TareMeasurement;

class GrainDeliveryController extends Controller
{
    // Создание новой поставки
    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'grain_type_id' => 'required|exists:grain_types,id',
            'volume' => 'required|numeric|min:0.1', // Брутто
            'delivery_date' => 'required|date',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:users,id',
        ]);

        $nettoVolume = $validated['volume'];
        $vehicle = Vehicle::with('latestTareMeasurement')->find($validated['vehicle_id']);
        $tareWeight = null;
        $maxWeight = null;
        $showTareReminder = false;

        if ($vehicle) {
            $maxWeight = $vehicle->max_weight;

            if (in_array($vehicle->type, ['привоз', 'универсальный'])) {
                $tare = $vehicle->latestTareMeasurement;

                if ($tare) {
                    $tareWeight = $tare->tare_weight;
                    $nettoVolume = max($validated['volume'] - $tareWeight, 0);
                    $tare->increment('delivery_count');

                    if ($tare->delivery_count >= 10) {
                        $tare->delete();
                        $showTareReminder = true;
                    }
                }
            }
        }


        $delivery = GrainDelivery::create(array_merge(
            $validated,
            [
                'volume' => $nettoVolume,
                'tare_weight' => $tareWeight,
                'max_weight' => $maxWeight
            ]
        ));

        WarehouseGrain::updateOrCreate(
            [
                'warehouse_id' => $delivery->warehouse_id,
                'grain_type_id' => $delivery->grain_type_id,
            ],
            []
        )->increment('amount', $delivery->volume);

        return response()->json([
            'message' => 'Поставка добавлена',
            'data' => $delivery,
            'showTareReminder' => $showTareReminder
        ]);
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
            'tare_weight',
            'delivery_date',
            'vehicle_id',
            'driver_id'
        ]);
        $oldWarehouseId = $record->warehouse_id;

        $validated = $request->validate([
            'grain_type_id' => 'required|exists:grain_types,id',
            'volume' => 'required|numeric|min:0.1', // NETTO или БРУТТО — зависит от tare_weight
            'delivery_date' => 'required|date',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:users,id',
            'tare_weight' => 'nullable|numeric|min:0'
        ]);

        // Если tare_weight передан — считаем, что volume уже NETTO
        $tareWeight = $validated['tare_weight'] ?? null;
        $nettoVolume = $validated['volume'];

        // Если нет тары — вычисляем сами
        if ($tareWeight === null) {
            $vehicle = Vehicle::with('latestTareMeasurement')->find($validated['vehicle_id']);

            if ($vehicle && in_array($vehicle->type, ['привоз', 'универсальный'])) {
                $tare = $vehicle->latestTareMeasurement;
                if ($tare) {
                    $tareWeight = $tare->tare_weight;
                    $nettoVolume = max($validated['volume'] - $tareWeight, 0);
                }
            }
        }

        $record->update(array_merge($validated, [
            'volume' => $nettoVolume,
            'tare_weight' => $tareWeight,
        ]));

        $new = $record->only([
            'grain_type_id',
            'volume',
            'tare_weight',
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
            $oldGrain = WarehouseGrain::firstOrCreate(
                ['warehouse_id' => $oldWarehouseId, 'grain_type_id' => $old['grain_type_id']],
                ['amount' => 0]
            );
            $oldGrain->amount -= $old['volume'];
            $oldGrain->amount = max($oldGrain->amount, 0);
            $oldGrain->save();

            $newGrain = WarehouseGrain::firstOrCreate(
                ['warehouse_id' => $oldWarehouseId, 'grain_type_id' => $new['grain_type_id']],
                ['amount' => 0]
            );
            $newGrain->amount += $new['volume'];
            $newGrain->save();
        }

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
