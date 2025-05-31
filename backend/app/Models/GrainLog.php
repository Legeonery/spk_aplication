<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrainLog extends Model
{
    protected $fillable = ['entity_type', 'entity_id', 'changes', 'action'];

    protected $casts = [
        'changes' => 'array',
    ];
}
