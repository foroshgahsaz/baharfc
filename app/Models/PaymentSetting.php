<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class PaymentSetting extends Model
{
    protected $fillable = ['payment_mode'];

    public static function current(): self
    {
        if (! Schema::hasTable('payment_settings')) {
            $model = new self(['payment_mode' => 'sandbox']);
            $model->exists = false;

            return $model;
        }

        return static::query()->firstOrCreate([], ['payment_mode' => 'sandbox']);
    }
}
