<?php

namespace App\Http\Controllers;

use App\Http\Requests\Warehouses\WarehousesStoreRequest;
use App\Http\Requests\Warehouses\WarehousesUpdateRequest;
use App\Http\Resources\Warehouses\WarehousesResource;
use App\Models\Warehouses;
use Illuminate\Http\Request;

class WarehousesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Warehouses::all();
        return WarehousesResource::collection($posts);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WarehousesStoreRequest $request)
    {
        $data = $request->validated();
        $post = Warehouses::create($data);

        return WarehousesResource::make($post);
    }

    /**
     * Display the specified resource.
     */
    public function show(Warehouses $warehouse)
    {
        return WarehousesResource::make($warehouse);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Warehouses $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WarehousesUpdateRequest $request, Warehouses $warehouse)
    {
        $data = $request->validated();
        $warehouse->update($data);

        $warehouse = $warehouse->fresh();

        return WarehousesResource::make($warehouse);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warehouses $warehouse)
    {
        $warehouse->delete();
        return response()->json([
            'message' => 'done'
        ]);
    }
}
