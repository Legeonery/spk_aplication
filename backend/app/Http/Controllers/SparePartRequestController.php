<?php

namespace App\Http\Controllers;

use App\Models\SparePartRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VehicleRepair;


class SparePartRequestController extends Controller
{
public function index()
{
    return SparePartRequest::with(['vehicle', 'spare_part'])->get();
}

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'quantity' => 'required|numeric|min:0.1',
            'note' => 'nullable|string',
            'spare_part_id' => 'nullable|exists:spare_parts,id',
            'custom_name' => 'nullable|string|required_without:spare_part_id',
        ]);

        $sparePartId = $request->spare_part_id;

        // Если запчасть новая — создаём её
        if (!$sparePartId && $request->custom_name) {
            $sparePart = \App\Models\SparePart::create([
                'name' => $request->custom_name,
            ]);
            $sparePartId = $sparePart->id;
        }

        $requestData = $request->only(['vehicle_id', 'quantity', 'note']);
        $requestData['spare_part_id'] = $sparePartId;
        $requestData['user_id'] = Auth::id();

        $created = SparePartRequest::create($requestData);

        return response()->json($created, 201);
    }


    public function updateStatus(Request $request, SparePartRequest $sparePartRequest)
    {
        $request->validate([
            'status' => 'required|in:Новая,В работе,Закрыта,Заказано,Получено,Выдана,Отклонено',
        ]);

        $newStatus = $request->status;
        $sparePartId = $sparePartRequest->spare_part_id;

        if (!$sparePartId) {
            return response()->json(['message' => 'Запчасть не указана'], 400);
        }

        $warehouseId = 2; // можно вынести в config

        // ✅ Обработка "Получено"
        if ($newStatus === 'Получено') {
            $stock = \App\Models\WarehouseSparePart::firstOrCreate(
                [
                    'warehouse_id' => $warehouseId,
                    'spare_part_id' => $sparePartId,
                ],
                ['quantity' => 0]
            );

            $stock->quantity += $sparePartRequest->quantity;
            $stock->save();

            \App\Models\SparePartMovement::create([
                'warehouse_id' => $warehouseId,
                'spare_part_id' => $sparePartId,
                'type' => 'in',
                'quantity' => $sparePartRequest->quantity,
                'reason' => 'Поступление по заявке',
                'date' => now(),
            ]);
        }

        // ✅ Обработка "Выдана"
        if ($newStatus === 'Выдана') {
            $stock = \App\Models\WarehouseSparePart::where([
                'warehouse_id' => $warehouseId,
                'spare_part_id' => $sparePartId,
            ])->first();

            if (!$stock || $stock->quantity < $sparePartRequest->quantity) {
                return response()->json(['message' => 'Недостаточно запчастей на складе'], 400);
            }

            $stock->quantity -= $sparePartRequest->quantity;
            $stock->save();

            \App\Models\SparePartMovement::create([
                'warehouse_id' => $warehouseId,
                'spare_part_id' => $sparePartId,
                'type' => 'out',
                'quantity' => $sparePartRequest->quantity,
                'reason' => 'Выдача по заявке',
                'date' => now(),
            ]);
        }

        $sparePartRequest->status = $newStatus;
        $sparePartRequest->save();

        return response()->json($sparePartRequest);
    }

    public function destroy(SparePartRequest $sparePartRequest)
    {
        if (Auth::user()->role_id !== 1) {
            return response()->json(['message' => 'Нет прав'], 403);
        }

        $sparePartRequest->delete();

        return response()->json(['message' => 'Удалено']);
    }
}
