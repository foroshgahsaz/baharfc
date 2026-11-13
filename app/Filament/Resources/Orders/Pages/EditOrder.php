<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('invoice')
                ->label('فاکتور')
                ->url(fn (): string => route('orders.invoice', ['order' => $this->getRecord(), 'format' => 'pdf']))
                ->openUrlInNewTab(),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('status')
                ->label('وضعیت')
                ->options(collect(OrderStatus::cases())->mapWithKeys(fn (OrderStatus $s) => [$s->value => $s->label()]))
                ->required(),
            Textarea::make('customer_note')->label('یادداشت مشتری')->disabled(),
        ]);
    }
}
