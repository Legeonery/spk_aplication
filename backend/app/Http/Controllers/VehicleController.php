<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;

class VehicleController extends Controller
{
    public function index()
    {
        return Vehicle::all();
    }
}
