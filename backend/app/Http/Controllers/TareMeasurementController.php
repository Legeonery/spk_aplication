<?php

namespace App\Http\Controllers;

use App\Models\TareMeasurement;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class TareMeasurementController extends Controller
{
    /**
     * Проверка, требуется ли измерение тары.
     */
    public function needsMeasurement(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $last = $vehicle->tareMeasurement;

        if ($vehicle->type === 'отгрузка') {
            return response()->json(['required' => true]);
        }

        if ($vehicle->type === 'привоз') {
            return response()->json([
                'required' => !$last || $last->delivery_count % 10 === 0,
            ]);
        }

        return response()->json(['required' => false]);
    }

    /**
     * Запись нового замера тары.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'tare_weight' => 'required|numeric|min:0',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $existing = TareMeasurement::where('vehicle_id', $vehicle->id)->first();

        if ($vehicle->type === 'привоз') {
            if ($existing && $existing->delivery_count % 10 !== 0) {
                // просто обновляем
                $existing->update([
                    'tare_weight' => $request->tare_weight,
                    'updated_at' => now()
                ]);
                return response()->json($existing);
            }

            // новый замер (либо каждые 10)
            $count = $existing ? $existing->delivery_count + 1 : 0;

            $existing?->delete();
            $tare = TareMeasurement::create([
                'vehicle_id' => $vehicle->id,
                'tare_weight' => $request->tare_weight,
                'delivery_count' => $count,
            ]);
        } else {
            // отгрузка — если уже есть запись, не создавать новую
            if ($existing) {
                return response()->json([
                    'message' => 'Замер уже существует для отгрузки.',
                    'data' => $existing
                ], 200);
            }

            $tare = TareMeasurement::create([
                'vehicle_id' => $vehicle->id,
                'tare_weight' => $request->tare_weight,
                'delivery_count' => 0, // фиксированно, если нужно ограничение
            ]);
        }

        return response()->json($tare, 201);
    }

    public function check(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
        ]);

        $exists = TareMeasurement::where('vehicle_id', $request->vehicle_id)->exists();

        return response()->json(['exists' => $exists]);
    }
}
