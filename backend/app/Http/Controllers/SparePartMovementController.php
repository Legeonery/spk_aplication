<?php

namespace App\Http\Controllers;

use App\Models\SparePart;
use App\Models\SparePartMovement;
use App\Models\WarehouseSparePart;
use Illuminate\Http\Request;

class SparePartMovementController extends Controller
{

    public function store(Request $request, $warehouseId)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'article' => 'nullable|string|max:255',
            'quantity' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'type' => 'required|in:in,out',
            'reason' => 'nullable|string|max:255',
        ]);

        // Запчасть — найдём или создадим
        $sparePart = SparePart::firstOrCreate(
            ['name' => $data['name'], 'article' => $data['article']]
        );

        // Обновляем остаток
        $stock = WarehouseSparePart::firstOrCreate([
            'warehouse_id' => $warehouseId,
            'spare_part_id' => $sparePart->id
        ]);

        if ($data['type'] === 'in') {
            $stock->increment('quantity', $data['quantity']);
        } elseif ($data['type'] === 'out') {
            if ($stock->quantity < $data['quantity']) {
                return response()->json(['message' => 'Недостаточно остатков'], 400);
            }
            $stock->decrement('quantity', $data['quantity']);
        }

        // Сохраняем движение
        $movement = SparePartMovement::create([
            'warehouse_id' => $warehouseId,
            'spare_part_id' => $sparePart->id,
            'quantity' => $data['quantity'],
            'date' => $data['date'],
            'type' => $data['type'],
            'reason' => $data['reason'] ?? null,
        ]);

        return response()->json($movement, 201);
    }
    public function getStock($warehouseId)
    {
        $stock = WarehouseSparePart::with('sparePart')
            ->where('warehouse_id', $warehouseId)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->sparePart->name,
                    'article' => $item->sparePart->article,
                    'quantity' => $item->quantity,
                ];
            });

        return response()->json($stock);
    }
}
