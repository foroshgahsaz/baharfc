<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\ChecksAdminPageAccess;
use App\Models\ShopSetting;
use App\Support\SchemaGuard;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use App\Filament\Forms\Components\MediaPicker;
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
    use ChecksAdminPageAccess;
    use InteractsWithSchemas;

    protected static function adminPagePermission(): string
    {
        return 'settings.shop';
    }

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
        $data = ShopSetting::current()->toArray();

        foreach (array_keys($data) as $key) {
            if (! SchemaGuard::hasColumn('shop_settings', $key)) {
                unset($data[$key]);
            }
        }

        $this->form->fill($data);
    }

    protected static function contactFormSettingsReady(): bool
    {
        return SchemaGuard::hasColumn('shop_settings', 'contact_form_enabled');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->statePath('data')
            ->components([
                TextInput::make('site_name')->label('عنوان سایت')->maxLength(120)->columnSpanFull(),
                TextInput::make('logo_wordmark')->label('متن لوگو (اگر تصویر ندارید)')->maxLength(80),
                MediaPicker::make('logo_path')
                    ->label('لوگو')
                    ->imagesOnly()
                    ->helperText('از کتابخانه رسانه انتخاب کنید یا ابتدا در «کتابخانهٔ رسانه» آپلود کنید.'),
                Textarea::make('site_description')->label('توضیح کوتاه فروشگاه')->rows(2)->columnSpanFull(),
                Textarea::make('meta_description')->label('توضیح SEO')->rows(2)->columnSpanFull(),
                ColorPicker::make('accent_color')->label('رنگ تأکید (برند)'),
                TextInput::make('contact_phone')->label('تلفن تماس')->tel(),
                TextInput::make('contact_email')->label('ایمیل تماس')->email(),
                Toggle::make('contact_form_enabled')
                    ->label('نمایش صفحه و فرم تماس در سایت')
                    ->default(true)
                    ->visible(fn (): bool => static::contactFormSettingsReady())
                    ->columnSpanFull(),
                TextInput::make('contact_form_title')
                    ->label('عنوان صفحه تماس')
                    ->default('تماس با ما')
                    ->maxLength(120)
                    ->visible(fn (): bool => static::contactFormSettingsReady())
                    ->columnSpanFull(),
                Textarea::make('contact_form_intro')
                    ->label('متن راهنما بالای فرم')
                    ->rows(3)
                    ->visible(fn (): bool => static::contactFormSettingsReady())
                    ->columnSpanFull(),
                TextInput::make('contact_form_success_message')
                    ->label('پیام پس از ارسال موفق')
                    ->maxLength(255)
                    ->visible(fn (): bool => static::contactFormSettingsReady())
                    ->columnSpanFull(),
                Toggle::make('contact_form_show_phone')
                    ->label('فیلد تلفن در فرم')
                    ->default(true)
                    ->visible(fn (): bool => static::contactFormSettingsReady()),
                Toggle::make('contact_form_require_phone')
                    ->label('تلفن اجباری')
                    ->default(false)
                    ->visible(fn (): bool => static::contactFormSettingsReady()),
                Toggle::make('contact_form_show_subject')
                    ->label('فیلد موضوع در فرم')
                    ->default(true)
                    ->visible(fn (): bool => static::contactFormSettingsReady()),
                Toggle::make('contact_form_require_subject')
                    ->label('موضوع اجباری')
                    ->default(false)
                    ->visible(fn (): bool => static::contactFormSettingsReady()),
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
            foreach (array_keys($state) as $key) {
                if (! SchemaGuard::hasColumn('shop_settings', $key)) {
                    unset($state[$key]);
                }
            }
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