<?php

namespace App\Models;

use App\Enums\TrafficEntityType;
use Illuminate\Database\Eloquent\Model;

class TrafficDailyStat extends Model
{
    protected $fillable = [
        'stat_date',
        'entity_type',
        'entity_key',
        'views',
    ];

    protected function casts(): array
    {
        return [
            'stat_date' => 'date',
            'entity_type' => TrafficEntityType::class,
        ];
    }
}