<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    public const PLAN_FREE = 'free';
    public const PLAN_PRO = 'pro';
    public const PLAN_PREMIUM = 'premium';

    protected $fillable = [
        'user_id',
        'plan',
        'expired_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'expired_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
