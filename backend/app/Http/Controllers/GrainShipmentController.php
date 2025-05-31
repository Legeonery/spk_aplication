<?php

namespace App\Http\Controllers;

use App\Models\GrainShipment;
use App\Models\WarehouseGrain;
use App\Models\GrainLog;
use Illuminate\Http\Request;

class GrainShipmentController extends Controller
{
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

        // === Корректировка остатков ===
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
            // Вернуть старый объем обратно
            $oldGrain = WarehouseGrain::firstOrCreate(
                ['warehouse_id' => $old['warehouse_id'], 'grain_type_id' => $old['grain_type_id']],
                ['amount' => 0]
            );
            $oldGrain->amount += $old['volume'];
            $oldGrain->save();

            // Вычесть новый объём
            $newGrain = WarehouseGrain::firstOrCreate(
                ['warehouse_id' => $new['warehouse_id'], 'grain_type_id' => $new['grain_type_id']],
                ['amount' => 0]
            );
            $newGrain->amount -= $new['volume'];
            $newGrain->amount = max(0, $newGrain->amount);
            $newGrain->save();
        }

        // === Логирование ===
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

        $shipment = GrainShipment::create($validated);

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

    public function byWarehouse($warehouseId)
    {
        $shipments = GrainShipment::with(['grainType', 'driver'])
            ->where('warehouse_id', $warehouseId)
            ->orderBy('shipment_date', 'desc')
            ->get();

        return response()->json($shipments);
    }
}
