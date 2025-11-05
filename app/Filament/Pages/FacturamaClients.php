<?php

namespace App\Filament\Pages;

use App\Services\FacturamaService;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Throwable;

class FacturamaClients extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static string $view = 'filament.pages.facturama-clients';
    protected static ?string $title = 'Clientes Facturama (API)';

    // --- Estado de Livewire ---
    // Colección para almacenar todos los datos de la API (colección vacía por defecto)
    public Collection $clients;
   
    public string $search = '';

    public function mount(FacturamaService $facturama): void
    {
        $this->clients = new Collection(); 
        
        try {
            // 1. Llama al servicio de la API
            $apiResponse = $facturama->getClients();

            // 2. Si la respuesta es una colección (Laravel), la "desenvuelve" para obtener el objeto principal
            if ($apiResponse instanceof Collection) {
                $apiResponse = $apiResponse->first();
            }
            
            // 3. Convierte la respuesta completa (incluyendo data) a un array asociativo
            $apiArray = json_decode(json_encode($apiResponse), true);

            // 4. Extrae la clave 'data' y asigna los registros (que ahora son arrays) a la propiedad pública
            // NOTA: Se mantienen como arrays para ser compatibles con $client['Rfc'] en la vista.
            $this->clients = collect($apiArray['data'] ?? []);

        } catch (Throwable $e) {
            // Manejo de errores: Si la API falla, la colección permanece vacía.
             \Log::error('Error al cargar clientes de Facturama: ' . $e->getMessage());
        }
    }

    /**
     * Propiedad computada de Livewire: $filteredClients
     * Retorna los datos de los clientes, aplicando el filtro de búsqueda.
     */
    #[Computed]
    public function getFilteredClientsProperty(): Collection
    {
        if (empty($this->search)) {
            return $this->clients;
        }

        $searchTerm = strtolower($this->search);

        return $this->clients->filter(function ($client) use ($searchTerm) {
            // Filtra por RFC o Nombre usando sintaxis de ARRAY ($client['Rfc'])
            return str_contains(strtolower($client['Rfc'] ?? ''), $searchTerm) ||
                   str_contains(strtolower($client['Name'] ?? ''), $searchTerm);
        });
    }
}