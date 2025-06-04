<?php

namespace App\Http\Controllers;

use App\Models\GrainTypes;
use Illuminate\Http\Request;


class GrainTypesController extends Controller
{
    public function index()
    {
        return response()->json(GrainTypes::all());
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:grain_types,name',
        ]);

        $grainType = GrainTypes::create($validated);

        return response()->json($grainType, 201);
    }
    public function destroy($id)
    {
        $grainType = GrainTypes::findOrFail($id);

        try {
            $grainType->delete();
            return response()->json(['message' => 'Удалено']);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'error' => 'Запись используется в других таблицах и не может быть удалена'
            ], 409); // 409 Conflict
        }
    }
}