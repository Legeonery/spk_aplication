<?php

namespace App\Http\Controllers;

use App\Models\SparePart;
use Illuminate\Http\Request;

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
}
