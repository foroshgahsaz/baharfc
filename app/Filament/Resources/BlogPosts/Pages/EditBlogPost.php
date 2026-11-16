<?php

namespace App\Filament\Resources\BlogPosts\Pages;

use App\Filament\Resources\BlogPosts\BlogPostResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBlogPost extends EditRecord
{
    protected static string $resource = BlogPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('پیش‌نمایش در سایت')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn () => route('blog.show', $this->record->slug))
                ->openUrlInNewTab()
                ->visible(fn () => $this->record->isVisibleOnSite()),
            DeleteAction::make(),
        ];
    }
}