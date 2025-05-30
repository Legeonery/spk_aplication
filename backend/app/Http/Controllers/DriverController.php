<?php

namespace App\Http\Controllers;

use App\Models\Driver;

class DriverController extends Controller
{
    public function index()
    {
        return Driver::all();
    }
}
