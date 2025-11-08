<?php

namespace App\Livewire;

use App\Models\Product;
use Filament\Notifications\Notification;
use Livewire\Component;

class PosTerminal extends Component
{
    // Propiedad que se sincronizará con el campo de búsqueda en la vista.
    public string $search = '';

    // Propiedad para almacenar los items del carrito. 
    // Cada ítem será un array: ['id', 'product_id', 'name', 'quantity', 'price', 'total']
    public array $cart = [];

    /**
     * Hook de Livewire que se ejecuta cada vez que la propiedad $search cambia.
     * Esto asegura que Livewire detecte y reaccione al cambio de la búsqueda.
     */
    public function updatedSearch()
    {
        // Livewire automáticamente re-ejecutará getFilteredProductsProperty y render.
        // Este hook sirve como punto de verificación y garantiza la reactividad.
        // Si tienes acceso a logs, puedes descomentar la línea de abajo para depurar:
        // Log::debug("Search updated to: " . $this->search);
    }


    /**
     * Propiedad computada que devuelve la lista de productos filtrados.
     */
    public function getFilteredProductsProperty(): array
    {
        $search = trim($this->search); // Usamos la propiedad $search actualizada.
        
        // 2. CONSTRUCCIÓN DE LA CONSULTA ELOQUENT
        $query = Product::select('id', 'name', 'price_salon')
                        ->orderBy('name');

        // 3. APLICAR FILTRO DE BÚSQUEDA
        if (!empty($search)) {
            $searchTerm = '%' . $search . '%';
            $query->where(function ($q) use ($searchTerm) {
                // Búsqueda por coincidencia en el nombre (case-insensitive según la DB)
                $q->where('name', 'like', $searchTerm);
            });
        }

        // 4. EJECUTAR LA CONSULTA Y CONVERTIR A ARREGLO
        return $query->limit(5)->get()->toArray(); 
    }
    
    // --- PROPIEDADES COMPUTADAS PARA CÁLCULO DE TOTALES ---
    
    /**
     * Calcula el subtotal de todos los artículos en el carrito.
     */
    public function getSubtotalProperty(): float
    {
        // Suma de la columna 'total' de todos los items en el array $this->cart
        return array_sum(array_column($this->cart, 'total'));
    }

    /**
     * Calcula el impuesto (IVA 16% asumido)
     */
    public function getTaxProperty(): float
    {
        // Tasa de IVA del 16%
        return $this->subtotal * 0.16; 
    }

    /**
     * Calcula el total final.
     */
    public function getTotalProperty(): float
    {
        return $this->subtotal + $this->tax;
    }


    // Método render que pasa los productos filtrados a la vista
    public function render()
    {
        return view('livewire.pos-terminal', [
            'products' => $this->filteredProducts,
        ]);
    }
    
    /**
     * Agrega un producto al carrito.
     */
    public function addToCart(int $productId, int $quantity = 1)
    {
        // Nos aseguramos de que la cantidad sea un número válido y positivo.
        if ($quantity < 1) {
            session()->flash('message', 'Error: La cantidad debe ser al menos 1.');
            return;
        }

        try {
            $product = Product::select('id', 'name', 'price_salon')->find($productId);
            
            if ($product) {
                // --- LÓGICA DE CARRITO: IMPLEMENTACIÓN DE AGREGACIÓN SIMPLE ---
                $cartItem = [
                    'id' => uniqid(), // ID único para el item del carrito
                    'product_id' => $productId,
                    'name' => $product->name,
                    'quantity' => (int) $quantity,
                    'price' => $product->price_salon,
                    'total' => $product->price_salon * (int) $quantity,
                ];
                
                // Añadir al carrito. Por simplicidad, agregamos un nuevo item siempre
                // en lugar de consolidar items existentes.
                $this->cart[] = $cartItem;

                Notification::make()
                    ->title('Producto(s) agregado')
                    ->success()
                    ->send();
                
                //session()->flash('message', '¡Éxito! Producto "' . $product->name . '" agregado al carrito (Cantidad: ' . $quantity . ').');
                $this->search = ''; // Limpiar la búsqueda al agregar
                
            } else {
                 session()->flash('message', 'Error: Producto no encontrado con ID: ' . $productId . '.');
            }
        } catch (\Exception $e) {
            Log::error('Error al agregar al carrito: ' . $e->getMessage());
            session()->flash('message', '¡Error! No se pudo agregar el producto. Mensaje: ' . $e->getMessage());
        }
    }
    
    /**
     * Quita un producto del carrito basado en su ID único (no el product_id).
     */
    public function removeFromCart(string $cartItemId)
    {
        // Filtrar el array del carrito para mantener solo los ítems cuyo 'id' no coincida
        $this->cart = array_filter($this->cart, fn($item) => $item['id'] !== $cartItemId);
        Notification::make()
                    ->title('Producto(s) borrado')
                    ->danger()
                    ->send();
        
       // session()->flash('message', 'Item removido del carrito.');
    }
}