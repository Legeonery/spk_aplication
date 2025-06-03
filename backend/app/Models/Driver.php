<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'license_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function licenseCategories()
    {
        return $this->belongsToMany(LicenseCategory::class, 'driver_license');
    }
}
