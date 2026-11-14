<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    protected $fillable = ['payment_mode'];

    public static function current(): self
    {
        return static::query()->firstOrCreate([], ['payment_mode' => 'sandbox']);
    }
}
