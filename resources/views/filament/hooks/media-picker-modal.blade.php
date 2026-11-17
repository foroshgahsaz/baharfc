@livewire('admin.media-picker-modal')

<script>
    const baharfcOpenMediaPicker = (trigger) => {
        if (! trigger) {
            return;
        }

        const statePath = trigger.getAttribute('data-state-path');
        const componentId = trigger.getAttribute('data-component-id');
        const imagesOnly = trigger.getAttribute('data-images-only') === '1';

        if (typeof Livewire === 'undefined' || ! statePath) {
            window.alert('کتابخانه رسانه آماده نیست. صفحه را رفرش کنید یا با پشتیبانی تماس بگیرید.');

            return;
        }

        Livewire.dispatchTo('admin.media-picker-modal', 'open-media-picker', {
            statePath,
            imagesOnly,
            componentId,
        });
    };

    const baharfcApplyMediaPick = (statePath, path, componentId) => {
        const applyTo = (component) => {
            if (! component) {
                return false;
            }

            try {
                if (component.$wire?.set) {
                    component.$wire.set(statePath, path);

                    return true;
                }

                component.set(statePath, path);

                return true;
            } catch (error) {
                console.error('media-picked: set failed', error);

                return false;
            }
        };

        if (componentId && applyTo(Livewire.find(componentId))) {
            return;
        }

        for (const component of Livewire.all()) {
            if (applyTo(component)) {
                return;
            }
        }

        window.alert('تصویر انتخاب شد اما در فرم اعمال نشد. صفحه را رفرش کنید و دوباره تلاش کنید.');
    };

    window.baharfcApplyMediaPick = baharfcApplyMediaPick;
    window.baharfcOpenMediaPicker = baharfcOpenMediaPicker;

    document.addEventListener('livewire:init', () => {
        document.addEventListener('click', (event) => {
            const trigger = event.target.closest('[data-baharfc-media-picker-open]');
            if (! trigger) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();
            baharfcOpenMediaPicker(trigger);
        }, true);

        Livewire.on('media-picked', (payload) => {
            const statePath = payload?.statePath;
            const path = payload?.path;
            const componentId = payload?.componentId;

            if (! statePath || path === undefined || path === null) {
                return;
            }

            baharfcApplyMediaPick(statePath, path, componentId);
        });
    });
</script>