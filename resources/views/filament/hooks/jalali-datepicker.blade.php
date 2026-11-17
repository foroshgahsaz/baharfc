<link rel="stylesheet" href="{{ asset('vendor/jalalidatepicker/jalalidatepicker.min.css') }}">
<script src="{{ asset('vendor/jalalidatepicker/jalalidatepicker.min.js') }}" defer></script>
<script>
    const baharfcStartJalaliWatch = () => {
        if (! window.jalaliDatepicker?.startWatch) {
            return;
        }

        window.jalaliDatepicker.startWatch({
            time: true,
            hasSecond: false,
            autoShow: true,
            autoHide: true,
            separatorChar: '/',
        });
    };

    document.addEventListener('DOMContentLoaded', baharfcStartJalaliWatch);
    document.addEventListener('livewire:init', () => {
        baharfcStartJalaliWatch();
        Livewire.hook('morph.updated', baharfcStartJalaliWatch);
    });
</script>