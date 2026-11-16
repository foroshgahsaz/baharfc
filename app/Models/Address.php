<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'title',
    'recipient_name',
    'phone',
    'province',
    'city',
    'address_line',
    'postal_code',
    'latitude',
    'longitude',
    'is_default',
])]
class Address extends Model
{
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fullLocation(): string
    {
        return "{$this->province}، {$this->city}";
    }
}
