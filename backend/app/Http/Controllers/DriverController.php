<?php

namespace App\Http\Controllers;

use App\Models\Driver;

class DriverController extends Controller
{
    public function index()
    {
        return response()->json(data: Driver::all());
    }
}
