<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\AdminRole;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
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
                    ->label('مدیر کل (همهٔ مجوزها)')
                    ->helperText('دسترسی کامل بدون نیاز به نقش — فقط برای صاحب فروشگاه')
                    ->live()
                    ->afterStateUpdated(function (bool $state, callable $set): void {
                        if ($state) {
                            $set('admin_role_id', null);
                        }
                    }),
                Select::make('admin_role_id')
                    ->label('نقش کارمند پنل')
                    ->options(fn (): array => (new User)->hasAdminRoleFeature()
                        ? AdminRole::query()->orderBy('name')->pluck('name', 'id')->all()
                        : [])
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->helperText('مثل وردپرس: نویسنده، مدیر فروشگاه و … — با «مدیر کل» خالی بماند')
                    ->visible(fn (callable $get): bool => ! $get('is_admin')
                        && optional(auth()->user())->hasAdminRoleFeature()),
                DateTimePicker::make('phone_verified_at')
                    ->label('تأیید موبایل'),
                DateTimePicker::make('email_verified_at')
                    ->label('تأیید ایمیل'),
            ]);
    }
}
