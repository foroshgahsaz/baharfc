<?php

namespace App\Filament\Resources\ProductQuestions\Pages;

use App\Filament\Resources\ProductQuestions\ProductQuestionResource;
use App\Models\ProductAnswer;
use Filament\Resources\Pages\EditRecord;

class EditProductQuestion extends EditRecord
{
    protected static string $resource = ProductQuestionResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $answer = ProductAnswer::query()
            ->where('product_question_id', $this->record->id)
            ->where('is_official', true)
            ->first();

        $data['answer_body'] = $answer?->body;

        return $data;
    }

    protected function afterSave(): void
    {
        $answer = trim((string) ($this->data['answer_body'] ?? ''));
        if ($answer === '') {
            return;
        }

        ProductAnswer::query()->updateOrCreate(
            ['product_question_id' => $this->record->id, 'is_official' => true],
            [
                'author_name' => 'فروشگاه',
                'body' => $answer,
                'status' => 'approved',
                'user_id' => auth()->id(),
            ],
        );
    }
}
