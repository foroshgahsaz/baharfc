@livewire('admin.media-picker-modal')

<script>
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