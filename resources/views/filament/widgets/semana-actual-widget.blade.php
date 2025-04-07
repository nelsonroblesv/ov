<x-filament::widget>
    <x-filament::card>
        <div class="text-sm text-gray-500">Semana Actual</div>
        <div class="text-xl font-bold text-primary-600">
            Estamos en semana: {{ $this->getSemana() }}
        </div>
    </x-filament::card>
</x-filament::widget>
