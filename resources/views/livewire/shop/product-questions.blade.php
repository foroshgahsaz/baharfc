@php
    use App\Support\JalaliDate;
@endphp

<div class="fs-product-questions-widget">
    @if ($questions->isNotEmpty())
        <ul class="fs-product-questions-list">
            @foreach ($questions as $question)
                <li class="fs-product-question-item pcard">
                    <p class="fs-product-question-body">
                        <strong>{{ $question->author_name }}:</strong>
                        {{ $question->body }}
                    </p>
                    @foreach ($question->approvedAnswers as $answer)
                        <p class="fs-product-answer-body">
                            <strong>{{ $answer->author_name }}:</strong>
                            {{ $answer->body }}
                        </p>
                    @endforeach
                </li>
            @endforeach
        </ul>
    @else
        <div class="fs-product-empty pcard">
            <p class="fs-product-empty-muted">هنوز پرسشی ثبت نشده است.</p>
        </div>
    @endif

    <div class="fs-product-ugc-form pcard">
        <h3>ثبت پرسش</h3>
        @if ($statusMessage)
            <p class="fs-product-ugc-success">{{ $statusMessage }}</p>
        @endif
        <form wire:submit="submit" class="fs-product-ugc-fields">
            @guest
                <label>
                    نام
                    <input type="text" wire:model="authorName" autocomplete="name" />
                </label>
                @error('authorName')<span class="fs-product-ugc-error">{{ $message }}</span>@enderror
                <label>
                    ایمیل
                    <input type="email" wire:model="authorEmail" autocomplete="email" />
                </label>
                @error('authorEmail')<span class="fs-product-ugc-error">{{ $message }}</span>@enderror
            @endguest
            <label>
                پرسش شما
                <textarea wire:model="body" rows="3"></textarea>
            </label>
            @error('body')<span class="fs-product-ugc-error">{{ $message }}</span>@enderror
            <button type="submit" class="fs-product-solid-btn" wire:loading.attr="disabled">ثبت پرسش</button>
        </form>
    </div>
</div>