<?php

namespace App\Http\Controllers;

use App\Models\SparePart;
use Illuminate\Http\Request;
use App\Models\SparePartVehicleLink;
class SparePartController extends Controller
{
    public function index()
    {
        return SparePart::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'article' => 'nullable|string|max:255',
        ]);

        $sparePart = SparePart::create($data);

        return response()->json($sparePart, 201);
    }

    public function update(Request $request, SparePart $sparePart)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'article' => 'nullable|string|max:255',
        ]);

        $sparePart->update($data);

        return response()->json($sparePart);
    }

    public function destroy(SparePart $sparePart)
    {
        $sparePart->delete();
        return response()->json(['message' => 'Удалено']);
    }
    public function available()
    {
        $warehouseId = 2; // или передавай через query-параметр, если складов несколько

        $parts = SparePart::with([
            'warehouseQuantities' => function ($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            }
        ])->get();

        $data = $parts->map(function ($part) use ($warehouseId) {
            $quantity = optional($part->warehouseQuantities->first())->quantity ?? 0;

            $vehicles = SparePartVehicleLink::where('spare_part_id', $part->id)
                ->join('vehicles', 'vehicles.id', '=', 'spare_part_vehicle_links.vehicle_id')
                ->select('vehicles.id', 'vehicles.name as vehicle_name', 'vehicles.number as vehicle_number')
                ->get();

            return [
                'id' => $part->id,
                'name' => $part->name,
                'quantity' => (float) $quantity,
                'notes' => null, // или добавь поле `notes` в spare_parts, если нужно
                'vehicles' => $vehicles,
                'status' => $quantity > 0 ? 'Доступна' : 'Не доступна',
            ];
        });

        return response()->json($data);
    }
}
