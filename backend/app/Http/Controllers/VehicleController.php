<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        return Vehicle::with(['driver', 'vehicleKind', 'latestTareMeasurement'])->get();
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'number' => 'required|string|unique:vehicles,number',
            'vehicle_kind_id' => 'required|exists:vehicle_kinds,id',
            'type' => 'required|string|in:работа в поле,привоз,отгрузка,универсальный',
            'status' => 'required|in:на ходу,на ремонте,не на ходу',
            'driver_id' => 'nullable|exists:drivers,id',
            'is_available' => 'boolean',
            'notes' => 'nullable|string',
        ];

        if (in_array($request->type, ['привоз', 'отгрузка', 'универсальный'])) {
            $rules['max_weight'] = 'required|numeric|min:0';
        } else {
            $rules['max_weight'] = 'nullable';
        }

        $data = $request->validate($rules);
        $vehicle = Vehicle::create($data);

        return response()->json($vehicle->load('vehicleKind', 'driver'), 201);
    }


    public function show(Vehicle $vehicle)
    {
        return $vehicle->load('vehicleKind', 'driver');
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $rules = [
            'name' => 'required|string',
            'number' => 'string|unique:vehicles,number,' . $vehicle->id,
            'vehicle_kind_id' => 'exists:vehicle_kinds,id',
            'type' => 'required|string|in:работа в поле,привоз,отгрузка,универсальный',
            'status' => 'in:на ходу,на ремонте,не на ходу',
            'driver_id' => 'nullable|exists:drivers,id',
            'is_available' => 'boolean',
            'notes' => 'nullable|string',
        ];

        if (in_array($request->type, ['привоз', 'отгрузка', 'универсальный'])) {
            $rules['max_weight'] = 'required|numeric|min:0';
        } else {
            $rules['max_weight'] = 'nullable';
        }

        $data = $request->validate($rules);
        $vehicle->update($data);

        return response()->json($vehicle->load('vehicleKind', 'driver'));
    }


    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return response()->noContent();
    }
    public function toggleAvailability(Vehicle $vehicle)
    {
        $vehicle->is_available = !$vehicle->is_available;
        $vehicle->save();

        return response()->json(['message' => 'Статус обновлён', 'vehicle' => $vehicle]);
    }
}
