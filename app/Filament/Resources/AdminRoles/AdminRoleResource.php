<?php

namespace App\Filament\Resources\AdminRoles;

use App\Filament\Concerns\ChecksAdminPermissions;
use App\Filament\Resources\AdminRoles\Pages\CreateAdminRole;
use App\Filament\Resources\AdminRoles\Pages\EditAdminRole;
use App\Filament\Resources\AdminRoles\Pages\ListAdminRoles;
use App\Models\AdminRole;
use App\Support\AdminPermissions;
use BackedEnum;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class AdminRoleResource extends Resource
{
    use ChecksAdminPermissions;

    protected static ?string $model = AdminRole::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?string $modelLabel = 'نقش';

    protected static ?string $pluralModelLabel = 'نقش‌های دسترسی';

    protected static ?string $navigationLabel = 'نقش‌های دسترسی';

    protected static string|UnitEnum|null $navigationGroup = 'دسترسی‌ها';

    protected static ?int $navigationSort = 1;

    protected static function adminPermissionPrefix(): string
    {
        return 'roles';
    }

    protected static function requiredDatabaseTable(): ?string
    {
        return 'admin_roles';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('نام نقش')
                    ->required()
                    ->maxLength(120)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set, $operation): void {
                        if ($operation === 'create' && filled($state)) {
                            $set('slug', Str::slug($state, '_'));
                        }
                    }),
                TextInput::make('slug')
                    ->label('شناسه یکتا')
                    ->required()
                    ->maxLength(60)
                    ->unique(ignoreRecord: true)
                    ->helperText('فقط حروف انگلیسی، عدد و زیرخط'),
                Textarea::make('description')
                    ->label('توضیح')
                    ->rows(2)
                    ->columnSpanFull(),
                CheckboxList::make('permissions')
                    ->label('مجوزها')
                    ->options(AdminPermissions::flatOptions())
                    ->columns(2)
                    ->bulkToggleable()
                    ->searchable()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نقش')->searchable(),
                TextColumn::make('slug')->label('شناسه')->badge(),
                TextColumn::make('users_count')->label('کاربران')->counts('users'),
                TextColumn::make('description')->label('توضیح')->limit(40),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdminRoles::route('/'),
            'create' => CreateAdminRole::route('/create'),
            'edit' => EditAdminRole::route('/{record}/edit'),
        ];
    }
}