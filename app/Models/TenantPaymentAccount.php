<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantPaymentAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'bank_name',
        'account_name',
        'account_number',
        'contact',
        'notes',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }
}

