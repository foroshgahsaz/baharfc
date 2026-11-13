<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'order_id',
    'gateway',
    'status',
    'amount',
    'reference',
    'tracking_code',
    'meta',
    'failure_reason',
    'paid_at',
])]
class Payment extends Model
{
    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            'meta' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
