<x-filament-widgets::widget>
    <x-filament::section :heading="$heading" :description="$description">
        
        {{-- Contenedor principal: define una cuadrícula de 6 columnas en dispositivos medianos (md) o más grandes --}}
        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
            
            {{-- Tarjeta de ejemplo. Cada una ocupa 1 columna (col-span-1) en la cuadrícula de 6 --}}
            <div class="col-span-1 p-3 bg-white dark:bg-gray-900 rounded-xl shadow ring-1 ring-gray-950/5 dark:ring-white/10">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Cuentas por cobrar</p>
                <p class="text-2xl font-bold mt-1 text-success-600 dark:text-success-400">
                    {{ number_format($stats['cuentas_por_cobrar']) }}
                </p>
            </div>

            <div class="col-span-1 p-3 bg-white dark:bg-gray-900 rounded-xl shadow ring-1 ring-gray-950/5 dark:ring-white/10">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Anticipos recibidos</p>
                <p class="text-2xl font-bold mt-1 text-success-600 dark:text-success-400">
                    {{ number_format($stats['anticipos_recibidos']) }}
                </p>
            </div>
            
            <div class="col-span-1 p-3 bg-white dark:bg-gray-900 rounded-xl shadow ring-1 ring-gray-950/5 dark:ring-white/10">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Anticipos entregados</p>
                <p class="text-2xl font-bold mt-1 text-success-600 dark:text-success-400">
                    {{ number_format($stats['anticipos_entregados']) }}
                </p>
            </div>

            <div class="col-span-1 p-3 bg-white dark:bg-gray-900 rounded-xl shadow ring-1 ring-gray-950/5 dark:ring-white/10">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pedidos por entregar</p>
                <p class="text-2xl font-bold mt-1 text-success-600 dark:text-success-400">
                    {{ number_format($stats['pedidos_por_entregar']) }}
                </p>
            </div>
            
            <div class="col-span-1 p-3 bg-white dark:bg-gray-900 rounded-xl shadow ring-1 ring-gray-950/5 dark:ring-white/10">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pedidos entregados</p>
                <p class="text-2xl font-bold mt-1 text-success-600 dark:text-success-400">
                    {{ number_format($stats['pedidos_entregados']) }}
                </p>
            </div>
            
            <div class="col-span-1 p-3 bg-white dark:bg-gray-900 rounded-xl shadow ring-1 ring-gray-950/5 dark:ring-white/10">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total de venta</p>
                <p class="text-2xl font-bold mt-1 text-success-600 dark:text-success-400">
                    {{ number_format($stats['total_venta']) }}
                </p>
            </div>
            
        </div>
        
    </x-filament::section>
</x-filament-widgets::widget>