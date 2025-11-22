<x-filament-panels::page>
    <form wire:submit.prevent="submit">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button tag="button" type="submit" form="submit">
                Save
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
