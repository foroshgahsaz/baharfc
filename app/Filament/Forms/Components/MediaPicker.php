<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;

class MediaPicker extends Field
{
    protected string $view = 'filament.forms.components.media-picker';

    protected bool $imagesOnly = true;

    public function imagesOnly(bool $condition = true): static
    {
        $this->imagesOnly = $condition;

        return $this;
    }

    public function acceptsImagesOnly(): bool
    {
        return $this->imagesOnly;
    }
}