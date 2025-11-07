<?php

namespace App\Filament\Pages;

use App\Models\Customer;
use App\Models\Pedido;
use App\Models\Product;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;

class POS extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.p-o-s';

    public $items;
    public $customers;
    public $pedidos;
    public $search = '';
    public $cart = [];

    public function mount()
    {
        $this->items = Product::all();
        $this->customers = Customer::all()->where('is_active');
        $this->pedidos = Pedido::all();
    }

    #[Computed]
    public function filteredItems()
    {
        if (empty($this->search)) {
            return $this->items;
        }

        return $this->items->filter(function ($item) {
            return str_contains(strtolower($item->name), strtolower($this->search));
        });
    }

    public function addToCart($itemId)
    {
        $key = array_search($itemId, array_column($this->cart, 'id'));

        if ($key !== false) {
            $this->cart[$key]['quantity']++;
        } else {
            $this->cart[] = [
                'id' => $itemId,
                'quantity' => 1,
            ];
        }
        // Opcional: Emitir un evento para notificar a otros componentes (ej: el contador del carrito)
        // $this->emit('cartUpdated');

        // Opcional: Mostrar una notificación
        // session()->flash('message', 'Producto agregado al carrito.');
    }

    public function getCartDetailsProperty()
    {
        if (empty($this->cart)) {
            return collect(); // Retorna una colección vacía para manejarlo fácilmente en Blade
        }

        $itemIds = array_column($this->cart, 'id');
        $itemsInCart = Product::whereIn('id', $itemIds)->get()->keyBy('id');

        // 1. Usamos 'mapWithKeys' para obtener la clave original del carrito
        $detailedCart = collect($this->cart)->mapWithKeys(function ($cartItem, $key) use ($itemsInCart) {
            $item = $itemsInCart->get($cartItem['id']);

            if ($item) {
                return [
                    $key => [ // 🎯 Aquí se usa la clave ($key) del array $this->cart
                        'key' => $key, // También la guardamos dentro del array para facilitar el acceso en Blade
                        'item' => $item,
                        'quantity' => $cartItem['quantity'],
                        'subtotal' => $item->price_salon * $cartItem['quantity'],
                    ]
                ];
            }
            return []; // Retornar vacío si no se encuentra el producto
        });

        return $detailedCart;
    }


    public function updatingCart($value, $key)
    {
        $parts = explode('.', $key);
        $cartIndex = $parts[0];

        // Si es vacío, no numérico o menor que 1, lo forzamos a 1
        if (empty($value) || !is_numeric($value) || $value < 1) {
            $saneValue = 1;
            $this->cart[$cartIndex]['quantity'] = $saneValue;
            return $saneValue;
        }

        // Forzamos a entero para evitar problemas de tipos de datos en la multiplicación
        $this->cart[$cartIndex]['quantity'] = (int)$value;
    }

    public function removeFromCart($cartKey)
    {
        // 1. Verificar si la clave existe en el array.
        if (isset($this->cart[$cartKey])) {
            // 2. Eliminar el elemento del arreglo.
            unset($this->cart[$cartKey]);

            // 3. Reindexar el array (opcional pero recomendado para mantener la estructura limpia)
            $this->cart = array_values($this->cart);

            // Livewire detectará el cambio y actualizará la vista (incluyendo $this->cartDetails)

            // Opcional: Mostrar una notificación
            // session()->flash('message', 'Producto eliminado del carrito.');
        }
    }

    public function getCartTotalSummaryProperty()
    {
        // Accedemos a los detalles del carrito para obtener la suma de subtotales por ítem
        $detailedCart = $this->cartDetails;

        // 1. Calcular el Subtotal General (suma de todos los subtotales de los ítems)
        $subtotal = $detailedCart->sum('subtotal');
        // 2. Definir la tasa de impuesto
        $taxRate = 0.16; // 16%

        // 3. Calcular el Impuesto (IVA)
        $tax = $subtotal * $taxRate;

        // 4. Calcular el Total General
        $total = $subtotal + $tax;

        // Retornamos un array con todos los valores
        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'tax_rate' => $taxRate * 100, // Para mostrar 16% en la vista
        ];
    }

    

    //checkout
    public function checkout(){
        //check if the cart is not empty 
        if (empty($this->cart)) {
            Notification::make()
            ->title('Error!')
            ->body('No hay artículos en el Pedido')
            ->danger()
            ->send();
            return;
        }

/*
        //create the sale... db transaction
        try {
            //code...
        
        DB::beginTransaction();

        //create a sale
        $sale = Sale::create([
            'total' => $this->total,
            'paid_amount' => $this->paid_amount,
            'customer_id' => $this->customer_id,
            'payment_method_id' => $this->payment_method_id,
            'discount' => $this->discount_amount
        ]);

        // create the sale items

        foreach($this->cart as $item){
            SalesItem::create([
                'sale_id' => $sale->id,
                'item_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);

            //update the stock
            $inventory = Inventory::where('item_id',$item['id'])->first();
            if ($inventory) {
                $inventory->quantity -= $item['quantity'];
                $inventory->save();
            }
        }

        DB::commit();

        //reset cart
        $this->cart = [];

        //reset other properties
        $this->search = '';
        $this->customer_id = null;
        $this->payment_method_id = null;
        $this->paid_amount = 0;
        $this->discount_amount = 0;

        Notification::make()
                ->title('Sale Completed')
                ->body('Do you want to print the receipt?')
                ->success()
                ->duration(10000)
                ->actions([
                    Action::make('print')
                        ->button()
                        ->label('Yes, Print Receipt')
                        ->url(route('sales.receipt', ['sale' => $sale->id]), shouldOpenInNewTab: true)
                        ->color('primary')
                        // ->url(route('sales.receipt', $sale))
                        ->openUrlInNewTab(false)
                        ->extraAttributes([
                            'onclick' => 'event.preventDefault(); printReceipt(this.href);'
                        ]),
                    // NotificationAction::make('cancel')
                    //     ->button()
                    //     ->label('No')
                    //     ->color('secondary'),
                ])
                ->send();


        } catch (\Exception $th) {
            DB::rollback();
            Notification::make()
            ->title('Failed Sale!')
            ->body('Failed to complete the sale, try again.')
            ->danger()
            ->send();
        }
            */
    }
 
}
