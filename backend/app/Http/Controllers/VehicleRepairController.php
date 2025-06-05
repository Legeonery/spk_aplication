<?php

namespace App\Http\Controllers;

use App\Models\VehicleRepair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleRepairController extends Controller
{

    public function index()
    {
        logger('user_id: ' . Auth::id());

        $repairs = VehicleRepair::with('vehicle')->get();
        foreach ($repairs as $r) {
            logger("repair {$r->id} vehicle_id: {$r->vehicle_id} driver_id: " . ($r->vehicle->driver_id ?? 'null'));
        }

        return $repairs->filter(function ($repair) {
            return $repair->vehicle && $repair->vehicle->driver_id === Auth::id();
        })->values();
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'reason' => 'required|string|min:3',
        ]);

        $exists = VehicleRepair::where('vehicle_id', $request->vehicle_id)->exists();
        if ($exists) {
            return response()->json(['message' => 'ТС уже в ремонте'], 422);
        }

        $repair = VehicleRepair::create($request->only(['vehicle_id', 'reason']));

        return response()->json($repair, 201);
    }

    public function destroy(VehicleRepair $vehicleRepair)
    {
        $vehicleRepair->delete();

        return response()->json(['message' => 'Удалено']);
    }
}
