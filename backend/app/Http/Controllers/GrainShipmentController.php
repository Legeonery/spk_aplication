<?php

namespace App\Http\Controllers;

use App\Models\GrainShipment;
use App\Models\WarehouseGrain;
use App\Models\GrainLog;
use App\Models\Vehicle;
use App\Models\TareMeasurement;
use Illuminate\Http\Request;

class GrainShipmentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id'   => 'required|exists:warehouses,id',
            'grain_type_id'  => 'required|exists:grain_types,id',
            'volume'         => 'required|numeric|min:0.1',
            'shipment_date'  => 'required|date',
            'vehicle_id'     => 'required|exists:vehicles,id',
            'driver_id'      => 'required|exists:drivers,id',
        ]);

        $vehicle = Vehicle::with('latestTareMeasurement')->findOrFail($validated['vehicle_id']);
        $tare = $vehicle->latestTareMeasurement;

        if (!$tare) {
            return response()->json(['message' => 'Необходимо выполнить замер тары перед отгрузкой.'], 409);
        }

        // Ограничение: 1 замер на 1 отгрузку
        if ($tare->delivery_count >= 1) {
            $tare->delete();
            return response()->json(['message' => 'Замер тары устарел. Выполните повторный замер.'], 409);
        }

        $shipment = GrainShipment::create($validated);

        // Удаляем замер тары после использования
        $tare->delete();

        $grain = WarehouseGrain::firstOrCreate(
            [
                'warehouse_id' => $shipment->warehouse_id,
                'grain_type_id' => $shipment->grain_type_id,
            ],
            ['amount' => 0]
        );

        $grain->amount -= $shipment->volume;
        $grain->amount = max(0, $grain->amount);
        $grain->save();

        return response()->json(['data' => $shipment], 201);
    }

    public function update(Request $request, $id)
    {
        $record = GrainShipment::findOrFail($id);
        $old = $record->only(['grain_type_id', 'volume', 'shipment_date', 'vehicle_id', 'driver_id', 'warehouse_id']);

        $validated = $request->validate([
            'grain_type_id' => 'required|exists:grain_types,id',
            'volume' => 'required|numeric|min:0.1',
            'shipment_date' => 'required|date',
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:drivers,id',
        ]);

        $record->update($validated);
        $new = $record->only(array_keys($old));

        if ($old['grain_type_id'] == $new['grain_type_id']) {
            $diff = $old['volume'] - $new['volume'];

            $grain = WarehouseGrain::firstOrCreate(
                ['warehouse_id' => $old['warehouse_id'], 'grain_type_id' => $old['grain_type_id']],
                ['amount' => 0]
            );

            $grain->amount += $diff;
            $grain->amount = max(0, $grain->amount);
            $grain->save();
        } else {
            $oldGrain = WarehouseGrain::firstOrCreate(
                ['warehouse_id' => $old['warehouse_id'], 'grain_type_id' => $old['grain_type_id']],
                ['amount' => 0]
            );
            $oldGrain->amount += $old['volume'];
            $oldGrain->save();

            $newGrain = WarehouseGrain::firstOrCreate(
                ['warehouse_id' => $new['warehouse_id'], 'grain_type_id' => $new['grain_type_id']],
                ['amount' => 0]
            );
            $newGrain->amount -= $new['volume'];
            $newGrain->amount = max(0, $newGrain->amount);
            $newGrain->save();
        }

        $changes = [];
        foreach ($old as $key => $value) {
            if ($value != $new[$key]) {
                $changes[$key] = [$value, $new[$key]];
            }
        }

        if (!empty($changes)) {
            GrainLog::create([
                'entity_type' => 'shipment',
                'entity_id' => $record->id,
                'changes' => $changes,
                'action' => 'updated',
            ]);
        }

        return response()->json(['data' => $record]);
    }

    public function byWarehouse($warehouseId)
    {
        $shipments = GrainShipment::with(['grainType', 'driver'])
            ->where('warehouse_id', $warehouseId)
            ->orderBy('shipment_date', 'desc')
            ->get();

        return response()->json($shipments);
    }
}
