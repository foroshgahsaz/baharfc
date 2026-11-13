<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('username')
                    ->label('نام کاربری')
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),
                TextInput::make('phone')
                    ->label('موبایل')
                    ->tel()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true),
                TextInput::make('email')
                    ->label('ایمیل')
                    ->email()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label('رمز عبور')
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),
                Toggle::make('is_admin')
                    ->label('دسترسی ادمین')
                    ->helperText('فقط برای کارکنان فروشگاه'),
                DateTimePicker::make('phone_verified_at')
                    ->label('تأیید موبایل'),
                DateTimePicker::make('email_verified_at')
                    ->label('تأیید ایمیل'),
            ]);
    }
}
