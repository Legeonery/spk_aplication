<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\LicenseCategory;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role','driver.licenseCategories')->where('is_active', true);

        if ($request->has('role')) {
            $query->whereHas('role', fn($q) => $q->where('name', $request->role));
        }

        return $query->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required',
            'categories' => 'nullable|string',
        ]);

        $role = Role::where('name', $validated['role'])->firstOrFail();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        if ($validated['role'] === 'Водитель') {
            $driver = Driver::create([
                'user_id' => $user->id,
                'license_number' => 'N/A',
            ]);

            $categoryCodes = array_map('trim', explode(',', $validated['categories'] ?? ''));
            $categoryIds = LicenseCategory::whereIn('code', $categoryCodes)->pluck('id');

            $driver->licenseCategories()->sync($categoryIds);
        }

        return response()->json(['message' => 'Пользователь создан', 'user' => $user->load('role')], 201);
    }
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'role' => 'required',
            'categories' => 'nullable|string',
            'password' => 'nullable|string|min:6',
        ]);

        $role = Role::where('name', $validated['role'])->firstOrFail();

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role_id = $role->id;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if ($validated['role'] === 'Водитель') {
            $driver = Driver::firstOrCreate(['user_id' => $user->id]);
            $categoryCodes = array_map('trim', explode(',', $validated['categories'] ?? ''));
            $categoryIds = LicenseCategory::whereIn('code', $categoryCodes)->pluck('id');
            $driver->licenseCategories()->sync($categoryIds);
        }

        return response()->json(['message' => 'Пользователь обновлён', 'user' => $user->load('role')]);
    }
}
