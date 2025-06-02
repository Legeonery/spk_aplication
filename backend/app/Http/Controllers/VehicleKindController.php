<?php

namespace App\Http\Controllers;

use App\Models\VehicleKind;
use Illuminate\Http\Request;

class VehicleKindController extends Controller
{
    public function index()
    {
        return VehicleKind::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:vehicle_kinds,name',
        ]);

        $kind = VehicleKind::create($data);

        return response()->json($kind, 201);
    }

    public function show(VehicleKind $vehicleKind)
    {
        return $vehicleKind;
    }

    public function update(Request $request, VehicleKind $vehicleKind)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:vehicle_kinds,name,' . $vehicleKind->id,
        ]);

        $vehicleKind->update($data);

        return response()->json($vehicleKind);
    }

    public function destroy(VehicleKind $vehicleKind)
    {
        $vehicleKind->delete();
        return response()->noContent();
    }
}
