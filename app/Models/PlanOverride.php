<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanOverride extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_key',
        'name',
        'price',
        'billing_cycle',
        'booking_limit_total',
        'benefit',
        'features',
        'feature_flags',
    ];

    protected $casts = [
        'booking_limit_total' => 'integer',
        'features' => 'array',
        'feature_flags' => 'array',
    ];
}

