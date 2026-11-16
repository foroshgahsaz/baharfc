<div class="mt-12 border-t border-slate-200 pt-10" id="comments">
    <h2 class="text-xl font-bold text-slate-900">نظرات ({{ $comments->count() }})</h2>

    @if ($statusMessage)
        <p class="mt-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ $statusMessage }}</p>
    @endif

    <div class="mt-8 space-y-6">
        @forelse ($comments as $comment)
            <div>
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                    <div class="flex flex-wrap items-baseline justify-between gap-2">
                        <p class="font-semibold text-slate-900">{{ $comment->author_name }}</p>
                        <time class="text-xs text-slate-400">@jalali($comment->created_at)</time>
                    </div>
                    <p class="mt-2 text-sm leading-relaxed text-slate-700">{{ $comment->body }}</p>
                    <button
                        type="button"
                        wire:click="setReplyTo({{ $comment->id }})"
                        class="mt-2 text-xs font-medium text-brand-600 hover:underline"
                    >
                        پاسخ
                    </button>
                </div>
                @foreach ($comment->children as $reply)
                    <div class="mr-6 mt-3 rounded-xl border border-slate-100 bg-white p-4">
                        <div class="flex flex-wrap items-baseline justify-between gap-2">
                            <p class="font-semibold text-slate-900">{{ $reply->author_name }}</p>
                            <time class="text-xs text-slate-400">@jalali($reply->created_at)</time>
                        </div>
                        <p class="mt-2 text-sm leading-relaxed text-slate-700">{{ $reply->body }}</p>
                    </div>
                @endforeach
            </div>
        @empty
            <p class="text-sm text-slate-500">هنوز نظری ثبت نشده است. اولین نفر باشید.</p>
        @endforelse
    </div>

    @if ($post->allow_comments)
        <form wire:submit="submit" class="mt-10 space-y-4 rounded-2xl border border-slate-200 bg-white p-6">
            @if ($parentId)
                <p class="text-sm text-slate-600">
                    در حال پاسخ به یک نظر هستید.
                    <button type="button" wire:click="cancelReply" class="text-brand-600 hover:underline">لغو</button>
                </p>
            @endif

            @guest
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">نام</label>
                        <input
                            type="text"
                            wire:model="authorName"
                            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        />
                        @error('authorName')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">ایمیل</label>
                        <input
                            type="email"
                            wire:model="authorEmail"
                            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        />
                        @error('authorEmail')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @endguest

            <div>
                <label class="block text-sm font-medium text-slate-700">نظر شما</label>
                <textarea
                    wire:model="body"
                    rows="4"
                    class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                    placeholder="نظر خود را بنویسید…"
                ></textarea>
                @error('body')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-700"
                wire:loading.attr="disabled"
            >
                ارسال نظر
            </button>
        </form>
    @else
        <p class="mt-8 text-sm text-slate-500">امکان ثبت نظر برای این مطلب غیرفعال است.</p>
    @endif
</div>