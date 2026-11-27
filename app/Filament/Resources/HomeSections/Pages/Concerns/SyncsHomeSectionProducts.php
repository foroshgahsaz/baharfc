<?php

namespace App\Filament\Resources\HomeSections\Pages\Concerns;

use App\Enums\HomeProductSource;

trait SyncsHomeSectionProducts
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['source'] ?? null) === HomeProductSource::Manual->value) {
            $data['category_id'] = null;
            $data['product_limit'] = $data['product_limit'] ?? 8;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->record;
        if (! $record) {
            return;
        }

        if ($record->source === HomeProductSource::Category) {
            $record->products()->detach();
        }
    }
}