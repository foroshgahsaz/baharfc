<?php

namespace App\Filament\Resources\ReturnRequests\Pages;

use App\Enums\ReturnRequestStatus;
use App\Filament\Resources\ReturnRequests\ReturnRequestResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditReturnRequest extends EditRecord
{
    protected static string $resource = ReturnRequestResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Textarea::make('reason')->label('دلیل مشتری')->disabled()->columnSpanFull(),
            Select::make('status')
                ->label('وضعیت')
                ->options(collect(ReturnRequestStatus::cases())->mapWithKeys(fn (ReturnRequestStatus $s) => [$s->value => $s->label()]))
                ->required(),
            Textarea::make('admin_note')->label('یادداشت مدیر')->columnSpanFull(),
        ]);
    }
}
