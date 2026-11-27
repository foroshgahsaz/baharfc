<?php

namespace App\Filament\Resources\HomeSections\Pages;

use App\Filament\Resources\HomeSections\HomeSectionResource;
use App\Filament\Resources\HomeSections\Pages\Concerns\SyncsHomeSectionProducts;
use Filament\Resources\Pages\CreateRecord;

class CreateHomeSection extends CreateRecord
{
    use SyncsHomeSectionProducts;

    protected static string $resource = HomeSectionResource::class;
}