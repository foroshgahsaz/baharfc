<?php

namespace App\Filament\Pages;

use App\Models\ShopSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ShopSettings extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'تنظیمات سایت';

    protected static ?string $title = 'تنظیمات عمومی فروشگاه';

    protected static string|UnitEnum|null $navigationGroup = 'تنظیمات';

    protected static ?int $navigationSort = -1;

    protected static ?string $slug = 'shop-settings';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(ShopSetting::current()->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->statePath('data')
            ->components([
                TextInput::make('site_name')->label('عنوان سایت')->maxLength(120)->columnSpanFull(),
                TextInput::make('logo_wordmark')->label('متن لوگو (اگر تصویر ندارید)')->maxLength(80),
                FileUpload::make('logo_path')
                    ->label('لوگو')
                    ->image()
                    ->disk('public')
                    ->directory('shop'),
                Textarea::make('site_description')->label('توضیح کوتاه فروشگاه')->rows(2)->columnSpanFull(),
                Textarea::make('meta_description')->label('توضیح SEO')->rows(2)->columnSpanFull(),
                ColorPicker::make('accent_color')->label('رنگ تأکید (برند)'),
                TextInput::make('contact_phone')->label('تلفن تماس')->tel(),
                TextInput::make('contact_email')->label('ایمیل تماس')->email(),
                Textarea::make('footer_text')->label('متن فوتر')->rows(2)->columnSpanFull(),
                Toggle::make('maintenance_enabled')->label('حالت در دست تعمیر')->columnSpanFull(),
                Textarea::make('maintenance_message')
                    ->label('پیام صفحه تعمیر')
                    ->rows(3)
                    ->columnSpanFull()
                    ->helperText('وقتی حالت تعمیر روشن است، مشتریان این متن را می‌بینند؛ کاربران ادمین همچنان به فروشگاه دسترسی دارند.'),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')
                            ->label('ذخیره تنظیمات')
                            ->submit('save'),
                    ]),
                ]),
        ]);
    }

    public function save(): void
    {
        try {
            $state = $this->form->getState();
            $setting = ShopSetting::current();
            if (! $setting->exists) {
                $setting = new ShopSetting;
            }
            $setting->fill($state);
            $setting->save();
        } catch (\Throwable $e) {
            $hint = str_contains($e->getMessage(), 'Permission denied') || str_contains($e->getMessage(), 'UnableToCreateDirectory')
                ? 'مجوز نوشتن روی دیسک آپلود نیست. روی سرور: php artisan baharfc:ensure-storage — یا chown www-data برای مسیر FILESYSTEM_PUBLIC_ROOT (مثلاً /data/baharfc/storage/app/public).'
                : null;

            Notification::make()
                ->title('ذخیره ناموفق')
                ->body(trim($hint ?: 'خطا در ذخیرهٔ تنظیمات. جزئیات در آرشیو خطاها.'))
                ->danger()
                ->send();

            report($e);

            return;
        }

        ShopSetting::clearCache();

        Notification::make()
            ->title('تنظیمات سایت ذخیره شد')
            ->success()
            ->send();
    }
}