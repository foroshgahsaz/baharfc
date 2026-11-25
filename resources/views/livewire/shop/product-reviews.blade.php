@php
    use App\Support\JalaliDate;
@endphp

<div class="fs-product-reviews-widget">
    <div class="fs-product-reviews-summary pcard">
        <p>
            <strong>{{ JalaliDate::toPersianDigits((string) $count) }}</strong>
            دیدگاه ثبت‌شده
            @if ($count > 0)
                <span class="fs-product-reviews-avg">— میانگین {{ JalaliDate::toPersianDigits((string) $average) }}</span>
            @endif
        </p>
    </div>

    @if ($reviews->isNotEmpty())
        <ul class="fs-product-reviews-list">
            @foreach ($reviews as $review)
                <li class="fs-product-review-item pcard">
                    <header>
                        <strong>{{ $review->author_name }}</strong>
                        <span class="fs-product-review-stars" aria-label="امتیاز {{ $review->rating }}">
                            {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                        </span>
                    </header>
                    <p>{{ $review->body }}</p>
                </li>
            @endforeach
        </ul>
    @endif

    <div class="fs-product-ugc-form pcard">
        <h3>ثبت دیدگاه</h3>
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
            <fieldset class="fs-product-star-field">
                <legend>امتیاز</legend>
                <div class="fs-product-star-input" role="radiogroup" aria-label="امتیاز از ۱ تا ۵">
                    @foreach (range(1, 5) as $value)
                        <button
                            type="button"
                            class="fs-product-star-input-btn"
                            wire:click="setRating({{ $value }})"
                            aria-label="{{ JalaliDate::toPersianDigits((string) $value) }} ستاره"
                            @if ($value <= $rating) data-active @endif
                        >
                            <span aria-hidden="true">★</span>
                        </button>
                    @endforeach
                </div>
            </fieldset>
            <label>
                متن دیدگاه
                <textarea wire:model="body" rows="4"></textarea>
            </label>
            @error('body')<span class="fs-product-ugc-error">{{ $message }}</span>@enderror
            <button type="submit" class="fs-product-solid-btn" wire:loading.attr="disabled">ثبت دیدگاه</button>
        </form>
    </div>
</div>