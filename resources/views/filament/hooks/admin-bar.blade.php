@php
    $adminBar = app(\App\Support\AdminBar\AdminBarBuilder::class)->forFilament(request());
@endphp

@if ($adminBar)
    <script>document.documentElement.classList.add('has-baharfc-admin-bar');</script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.body?.classList.add('has-baharfc-admin-bar');
        });
    </script>
    <x-admin-bar :admin-bar="$adminBar" />
@endif