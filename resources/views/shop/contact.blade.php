@extends('layouts.shop')

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-slate-900">{{ $settings->contact_form_title ?: 'تماس با ما' }}</h1>

        @if ($settings->contact_form_intro)
            <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $settings->contact_form_intro }}</p>
        @endif

        @if ($settings->contact_phone || $settings->contact_email)
            <ul class="mt-6 space-y-2 text-sm text-slate-600">
                @if ($settings->contact_phone)
                    <li>
                        <span class="font-medium text-slate-800">تلفن:</span>
                        <a href="tel:{{ $settings->contact_phone }}" class="text-brand-600 hover:underline">{{ $settings->contact_phone }}</a>
                    </li>
                @endif
                @if ($settings->contact_email)
                    <li>
                        <span class="font-medium text-slate-800">ایمیل:</span>
                        <a href="mailto:{{ $settings->contact_email }}" class="text-brand-600 hover:underline">{{ $settings->contact_email }}</a>
                    </li>
                @endif
            </ul>
        @endif

        <div class="mt-10">
            <livewire:shop.contact-form />
        </div>
    </div>
@endsection