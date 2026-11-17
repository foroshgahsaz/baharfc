@livewire('admin.media-picker-modal')

<script>
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-baharfc-media-picker-open]');
        if (! trigger) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        const statePath = trigger.getAttribute('data-state-path');
        const componentId = trigger.getAttribute('data-component-id');
        const imagesOnly = trigger.getAttribute('data-images-only') === '1';

        if (typeof Livewire === 'undefined' || ! statePath) {
            return;
        }

        Livewire.dispatchTo('admin.media-picker-modal', 'open-media-picker', {
            statePath,
            imagesOnly,
            componentId,
        });
    }, true);

    document.addEventListener('livewire:init', () => {
        Livewire.on('media-picked', (payload) => {
            const statePath = payload?.statePath;
            const path = payload?.path;
            const componentId = payload?.componentId;

            if (! statePath || path === undefined || path === null) {
                return;
            }

            const applyTo = (component) => {
                if (! component) {
                    return false;
                }

                try {
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

            Livewire.all().forEach((component) => applyTo(component));
        });
    });
</script>