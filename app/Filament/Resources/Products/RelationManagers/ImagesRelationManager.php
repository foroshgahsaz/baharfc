<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Filament\Forms\Components\MediaPicker;
use App\Filament\Forms\Components\MultiMediaPicker;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'گالری تصاویر';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            MediaPicker::make('path')
                ->label('تصویر')
                ->imagesOnly()
                ->required()
                ->validationMessages([
                    'required' => 'لطفاً یک تصویر از کتابخانه انتخاب کنید.',
                ]),
            TextInput::make('alt')->label('متن جایگزین'),
            TextInput::make('sort_order')->label('ترتیب')->numeric()->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('path')->label('پیش‌نمایش')->disk('public')->square(),
                TextColumn::make('alt')->label('Alt'),
                TextColumn::make('sort_order')->label('ترتیب'),
            ])
            ->headerActions([
                CreateAction::make()->label('افزودن تصویر'),
                Action::make('bulkAdd')
                    ->label('افزودن چند تصویر')
                    ->icon('heroicon-o-photo')
                    ->modalHeading('افزودن چند تصویر به گالری')
                    ->modalSubmitActionLabel('افزودن به گالری')
                    ->form([
                        MultiMediaPicker::make('paths')
                            ->label('تصاویر')
                            ->imagesOnly()
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $product = $this->getOwnerRecord();
                        $paths = array_values(array_filter((array) ($data['paths'] ?? [])));

                        if ($paths === []) {
                            return;
                        }

                        $sort = ((int) $product->images()->max('sort_order')) + 1;

                        foreach ($paths as $path) {
                            $product->images()->create([
                                'path' => $path,
                                'sort_order' => $sort++,
                            ]);
                        }

                        Notification::make()
                            ->title(count($paths).' تصویر به گالری اضافه شد')
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
