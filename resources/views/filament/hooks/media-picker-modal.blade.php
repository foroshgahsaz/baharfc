@livewire('admin.media-picker-modal')

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('media-picked', ({ statePath, path, componentId }) => {
            if (! componentId || ! statePath) {
                return;
            }

            const component = Livewire.find(componentId);
            if (component) {
                component.set(statePath, path);
            }
        });
    });
</script>