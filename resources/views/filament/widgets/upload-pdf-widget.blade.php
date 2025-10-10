<x-filament::widget>
    <x-filament::card>
        <form wire:submit.prevent="submit">
            {{ $this->form }}
            <x-filament::button type="submit" class="mt-4" wire:loading.attr="disabled">
                <span wire:loading.remove>Отправить</span>
                <span wire:loading>Загружается...</span>
            </x-filament::button>
        </form>
    </x-filament::card>
</x-filament::widget>
