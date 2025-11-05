<?php

namespace App\Filament\Pages;

use App\Services\FacturamaService;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Throwable;

class FacturamaProducts extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static string $view = 'filament.pages.facturama-products';
    protected static ?string $title = 'Productos Facturama (API)';

    // --- Estado de Livewire ---
    // Colección para almacenar todos los datos de la API (colección vacía por defecto)
    public Collection $products;
   
    public string $search = '';

    public function mount(FacturamaService $facturama): void
    {
        $this->products = new Collection(); 
        
        try {
            // 1. Llama al servicio de la API
            $apiResponse = $facturama->getProducts();

            // 2. Si la respuesta es una colección (Laravel), la "desenvuelve" para obtener el objeto principal
            if ($apiResponse instanceof Collection) {
                $apiResponse = $apiResponse->first();
            }
            
            // 3. Convierte la respuesta completa (incluyendo data) a un array asociativo
            $apiArray = json_decode(json_encode($apiResponse), true);

            // 4. Extrae la clave 'data' y asigna los registros (que ahora son arrays) a la propiedad pública
            // NOTA: Se mantienen como arrays para ser compatibles con $client['Rfc'] en la vista.
            $this->products = collect($apiArray['data'] ?? []);

        } catch (Throwable $e) {
            // Manejo de errores: Si la API falla, la colección permanece vacía.
             \Log::error('Error al cargar productos de Facturama: ' . $e->getMessage());
        }
    }

    /**
     * Propiedad computada de Livewire: $filteredProducts
     * Retorna los datos de los clientes, aplicando el filtro de búsqueda.
     */
    #[Computed]
    public function getFilteredProductsProperty(): Collection
    {
        if (empty($this->search)) {
            return $this->products;
        }

        $searchTerm = strtolower($this->search);

        return $this->products->filter(function ($product) use ($searchTerm) {
           
            return str_contains(strtolower($product['Name'] ?? ''), $searchTerm);
        });
    }
        
}
