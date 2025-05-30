<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'type',
        'max_weight',
        'status',
        'driver_id',
        'is_available',
        'notes',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}