<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Product;
use Filament\Notifications\Notification;
use Livewire\Component;

class PosTerminal extends Component
{
    public string $search = '';
    public $customers = []; // Lista completa de clientes
    public $selectedCustomerId = '';

    // Propiedad para almacenar los items del carrito. 
    // Cada ítem será un array: ['id', 'product_id', 'name', 'quantity', 'price', 'total']
    public array $cart = [];

    /***Clientes  */
    public function updatedSelectedCustomerId($value)
    {
        // Lógica que se ejecuta cuando el cliente cambia (ej: aplicar descuentos, etc.)
    }

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
        return $query->limit(10)->get()->toArray();
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

    public function mount()
    {
        $this->customers = Customer::query()->where('is_active', true)->orderBy('name')->get();
    }

    /**
     * Agrega un producto al carrito.
     */
     public function addToCart($productId, $quantity = 1)
    {
        $product = collect(Product::all())->firstWhere('id', $productId);

        if (!$product) {
            session()->flash('message', '¡Error! Producto no encontrado.');
            return;
        }

        // Usamos el ID del producto como clave temporal para fácil acceso
        $cartItemId = $productId; 
        $quantity = max(1, (int) $quantity); // Asegurar que la cantidad sea al menos 1
        $price = $product['price_salon'];

        if (isset($this->cart[$cartItemId])) {
            // Actualizar cantidad y total si ya existe
            $this->cart[$cartItemId]['quantity'] += $quantity;
            $this->cart[$cartItemId]['total'] = $this->cart[$cartItemId]['quantity'] * $price;
        } else {
            // Agregar nuevo ítem al carrito
            $this->cart[$cartItemId] = [
                'id' => $productId,
                'name' => $product['name'],
                'quantity' => $quantity,
                'price' => $price,
                'total' => $quantity * $price,
            ];
        }
        
        Notification::make()
            ->title('Producto agregado al Pedido.')
            ->success()
            ->send();

        $this->search = ''; // Limpiar búsqueda después de agregar
    }

    /**
     * Quita un producto del carrito basado en su ID único (no el product_id).
     */
     public function removeFromCart($cartItemId)
    {
        if (isset($this->cart[$cartItemId])) {
            unset($this->cart[$cartItemId]);

            Notification::make()
            ->title('Producto borrado del Pedido.')
            ->danger()
            ->send();

        } else {
            session()->flash('message', 'Error al remover: Producto no estaba en el carrito.');
        }
    }

    public function getCartQuantityProperty()
    {
        // Usamos array_column para obtener todas las cantidades, y luego sumamos
        return array_sum(array_column($this->cart, 'quantity'));
    }
}
