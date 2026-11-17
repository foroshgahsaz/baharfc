<?php

namespace App\Filament\Forms\Components;

use App\Support\JalaliDate;
use Filament\Forms\Components\TextInput;

class JalaliDateTimePicker extends TextInput
{
    protected bool $withTime = true;

    public function time(bool $condition = true): static
    {
        $this->withTime = $condition;

        return $this;
    }

    public function acceptsTime(): bool
    {
        return $this->withTime;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->placeholder($this->withTime ? '۱۴۰۴/۰۸/۲۶ ۱۴:۳۰' : '۱۴۰۴/۰۸/۲۶')
            ->helperText($this->withTime
                ? 'تقویم شمسی — ساعت اختیاری؛ در پایگاه داده میلادی ذخیره می‌شود.'
                : 'تقویم شمسی؛ در پایگاه داده میلادی ذخیره می‌شود.')
            ->extraInputAttributes(fn (): array => [
                'data-jdp' => true,
                'data-jdp-time' => $this->acceptsTime() ? 'true' : 'false',
                'dir' => 'ltr',
                'autocomplete' => 'off',
            ])
            ->formatStateUsing(function (mixed $state): ?string {
                if ($state === null || $state === '') {
                    return null;
                }

                return $this->withTime
                    ? JalaliDate::dateTime($state)
                    : JalaliDate::date($state);
            })
            ->dehydrateStateUsing(function (?string $state): mixed {
                if ($state === null || trim($state) === '') {
                    return null;
                }

                return JalaliDate::fromFormString($state, $this->withTime);
            });
    }
}