<div class="p-4 bg-white shadow rounded-xl dark:bg-gray-800">
<h2 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-white">Listado de Clientes</h2>

<!-- Input de Búsqueda con Livewire (busca al escribir) -->
<div class="mb-6">
    <input 
        wire:model.live.debounce.300ms="search" 
        type="text" 
        placeholder="Buscar por RFC o Nombre..."
        class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition duration-150"
    >
</div>

<!-- Tabla de Clientes con Tailwind CSS -->
<div class="overflow-x-auto border border-gray-200 rounded-lg dark:border-gray-700">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700:width:100%">
        <thead>
            <tr class="bg-indigo-600 dark:bg-indigo-800">
                <th class="px-6 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">ID Cliente</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">RFC</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">Uso CFDI</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">Régimen Fiscal</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
            @forelse ($this->filteredClients as $client)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $client['Id'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $client['Rfc'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $client['Name'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $client['CfdiUse'] ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $client['FiscalRegime'] ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-base font-medium text-gray-500 dark:text-gray-400">
                        No se encontraron clientes que coincidan con la búsqueda.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
   
</div>


</div>