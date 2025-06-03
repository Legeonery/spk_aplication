<?php

namespace App\Http\Controllers;

use App\Models\LicenseCategory;

class LicenseCategoryController extends Controller
{
    public function index()
    {
        return LicenseCategory::all();
    }
}