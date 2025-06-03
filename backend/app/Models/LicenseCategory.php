<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseCategory extends Model
{
    protected $fillable = ['code', 'description'];

    public function drivers()
    {
        return $this->belongsToMany(Driver::class, 'driver_license');
    }
}