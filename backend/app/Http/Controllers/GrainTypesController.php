<?php

namespace App\Http\Controllers;

use App\Models\GrainTypes;


class GrainTypesController extends Controller
{
    public function index()
    {
        return response()->json(GrainTypes::all());
    }
}