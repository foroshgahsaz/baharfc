<?php

namespace App\Services;

use App\Contracts\SmsSender;
use Illuminate\Support\Facades\Log;

class LogSmsSender implements SmsSender
{
    public function send(string $phone, string $message): void
    {
        Log::channel('single')->info('SMS', [
            'phone' => $phone,
            'message' => $message,
        ]);
    }
}
