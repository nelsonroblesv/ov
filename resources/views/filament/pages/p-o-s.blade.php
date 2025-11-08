<x-filament-panels::page>
    {{-- 
        PASO CRÍTICO 3: Incluir el componente Livewire. 
        Esto asegura que Livewire lo inicialice y gestione sus propiedades.
    --}}
    @livewire(\App\Livewire\PosTerminal::class)
</x-filament-panels::page>