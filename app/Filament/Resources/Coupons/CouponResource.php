<?php

namespace App\Filament\Resources\Coupons;

use App\Enums\CouponType;
use App\Filament\Resources\Coupons\Pages\CreateCoupon;
use App\Filament\Resources\Coupons\Pages\EditCoupon;
use App\Filament\Resources\Coupons\Pages\ListCoupons;
use App\Models\Coupon;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static ?string $modelLabel = 'کد تخفیف';

    protected static ?string $pluralModelLabel = 'کدهای تخفیف';

    protected static ?string $navigationLabel = 'کدهای تخفیف';

    protected static string|UnitEnum|null $navigationGroup = 'فروشگاه';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')
                ->label('کد')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(50)
                ->extraInputAttributes(['dir' => 'ltr', 'class' => 'text-left']),
            Select::make('type')
                ->label('نوع')
                ->options([
                    CouponType::Percent->value => 'درصدی',
                    CouponType::Fixed->value => 'مبلغ ثابت',
                ])
                ->required()
                ->native(false),
            TextInput::make('value')
                ->label('مقدار')
                ->required()
                ->numeric()
                ->minValue(1)
                ->helperText('برای درصدی: ۱–۱۰۰؛ برای ثابت: مبلغ به تومان'),
            TextInput::make('min_order_amount')
                ->label('حداقل مبلغ سفارش (تومان)')
                ->numeric()
                ->default(0)
                ->minValue(0),
            TextInput::make('max_uses')
                ->label('سقف استفاده')
                ->numeric()
                ->minValue(1)
                ->helperText('خالی = نامحدود'),
            TextInput::make('used_count')
                ->label('تعداد استفاده‌شده')
                ->numeric()
                ->default(0)
                ->disabled()
                ->dehydrated(false),
            DateTimePicker::make('starts_at')->label('شروع اعتبار'),
            DateTimePicker::make('ends_at')->label('پایان اعتبار'),
            Toggle::make('is_active')->label('فعال')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('کد')->searchable()->copyable(),
                TextColumn::make('type')->label('نوع')->badge(),
                TextColumn::make('value')->label('مقدار'),
                TextColumn::make('used_count')->label('استفاده'),
                TextColumn::make('max_uses')->label('سقف')->placeholder('∞'),
                IconColumn::make('is_active')->label('فعال')->boolean(),
            ])
            ->defaultSort('code');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCoupons::route('/'),
            'create' => CreateCoupon::route('/create'),
            'edit' => EditCoupon::route('/{record}/edit'),
        ];
    }
}
