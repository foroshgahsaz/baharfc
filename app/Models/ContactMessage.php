<?php

namespace App\Models;

use App\Enums\ContactMessageStatus;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
        'ip',
        'admin_note',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ContactMessageStatus::class,
            'read_at' => 'datetime',
        ];
    }

    public function markAsRead(): void
    {
        if ($this->status === ContactMessageStatus::New) {
            $this->forceFill([
                'status' => ContactMessageStatus::Read,
                'read_at' => now(),
            ])->save();
        }
    }
}