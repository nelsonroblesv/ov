<div
    class="flex flex-col md:flex-row w-full min-h-screen pos-terminal-container antialiased text-gray-800 dark:text-gray-100">


    <div class="w-full md:w-2/3 p-4 md:p-6 flex flex-col space-y-6 overflow-y-hidden">
        <!-- Encabezado -->
        

        <!-- Buscador -->
        <div class="w-full flex-shrink-0">
            <input type="text" placeholder="Escribe el nombre del producto..."
                class="w-full p-3 border border-gray-300 dark:border-neutral-700 rounded-lg shadow-sm bg-white dark:bg-neutral-800 text-gray-800 dark:text-gray-100 placeholder-gray-400 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150"
                wire:model.live="search">
        </div>
        <!-- Tabla de productos -->
        <div class="flex-grow overflow-y-auto min-h-0">
            <div class=" dark:bg-neutral-800 overflow-hidden"
                wire:loading.class="opacity-75" wire:target="search">

                <h2 class="text-xl font-semibold p-4 border-b dark:border-neutral-700 text-gray-900 dark:text-gray-100">
                    Lista de Productos ({{ count($products) }} resultados)
                </h2>

                <div class="gap-4 bg-gray-50 dark:bg-neutral-700 px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-300"
                    style="display: flex;">
                    <div class="col-span-3" style="flex:1">Producto</div>
                    <div class="col-span-1 text-left" style="flex:1">Precio</div>
                    <div class="col-span-2 text-right" style="flex:1">Cantidad</div>
                </div>

                <div class="divide-y divide-gray-200 dark:divide-neutral-700 max-h-[80vh] overflow-y-auto">

                    @forelse ($products as $item)
                        <div wire:key="product-{{ $item['id'] }}" x-data="{
                            quantity: 1,
                            itemId: {{ $item['id'] }},
                            addToCartAction() {
                                $wire.addToCart(this.itemId, this.quantity);
                                this.quantity = 1;
                            }
                        }"
                            class="gap-4 px-3 py-3 items-center"
                            style="display: flex;flex:1">

                            <div class="col-span-3" style="display: flex;flex:1">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                    {{ $item['name'] }}</h4>
                            </div>

                            <div class="col-span-1 text-left text-xs text-gray-700 dark:text-gray-300 " 
                                style="display: flex;flex:1">
                                $ {{ number_format($item['price_salon'], 2) }}
                            </div>

                            <div class="col-span-2 text-right flex justify-end items-center space-x-2">

                                <input type="number" x-model="quantity" min="1"
                                    class="w-10 p-1 text-center border border-gray-300 dark:border-neutral-700 rounded-md bg-white dark:bg-neutral-900 text-gray-800 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150">

                                <button x-on:click="addToCartAction()" wire:loading.attr="disabled"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                                    <span wire:loading.remove wire:target="addToCart({{ $item['id'] }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                    <span wire:loading wire:target="addToCart({{ $item['id'] }})">
                                        ...
                                    </span>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                            No se encontraron productos disponibles que coincidan con "{{ $search }}".
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div
        class="w-full md:w-1/3 bg-white dark:bg-neutral-800 border-t md:border-t-0 md:border-l border-gray-200 dark:border-neutral-700 p-4 md:p-6 flex flex-col shadow-2xl overflow-y-auto rounded-xl">

        <h2
            class="text-2xl font-bold mb-4 text-indigo-600 dark:text-indigo-400 border-b pb-2 dark:border-neutral-700 flex-shrink-0">
            Detalle del Pedido
        </h2>

        <details open class="border border-gray-200 dark:border-neutral-700 rounded-lg shadow-md mb-4 flex-shrink-0 ">
            <summary
                class="p-3 font-bold cursor-pointer bg-gray-50 dark:bg-neutral-700 rounded-t-lg flex justify-between items-center text-gray-800 dark:text-gray-100">
                <span>Productos agregados ({{ count($cart) }})</span>
                <svg class="h-5 w-5 transition-transform duration-200 transform details-arrow"
                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </summary>

            <!-- Contenedor de la lista de items en el carrito -->
            <div class="p-4 max-h-64 overflow-y-auto space-y-3 bg-white dark:bg-neutral-800">

                {{-- ITERACIÓN SOBRE EL CARRITO REAL --}}
                @forelse ($cart as $cartItem)
                    <div wire:key="cart-{{ $cartItem['id'] }}"
                        class="flex justify-between items-center border-b pb-2 last:border-b-0 dark:border-neutral-700">
                        <div class="flex flex-col">
                            <span class="text-sm font-medium">{{ $cartItem['name'] }}</span>
                            <span class="text-xs text-gray-500 dark:text-neutral-400">
                                {{ $cartItem['quantity'] }} x $ {{ number_format($cartItem['price'], 2) }}
                            </span>
                        </div>

                        <div class="flex items-center space-x-2 font-bold">
                            {{-- Total del ítem --}}
                            <span>$ {{ number_format($cartItem['total'], 2) }}</span>

                            {{-- BOTÓN DE ELIMINAR --}}
                            <button class="text-red-500 hover:text-red-700 transition duration-150"
                                wire:click="removeFromCart('{{ $cartItem['id'] }}')" wire:loading.attr="disabled">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-2 0v6a1 1 0 102 0V7z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-gray-400 dark:text-neutral-500">
                        El pedido está vacío. ¡Agrega un producto!
                    </div>
                @endforelse
            </div>
        </details>

        <div class="pt-4 border-t border-gray-200 dark:border-neutral-700 space-y-4 flex-shrink-0">
            <div class="space-y-2 text-sm">
                <div class="flex justify-between font-medium">
                    <span>Subtotal:</span>
                    <span class="font-semibold">$ {{ number_format($this->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between" style="color: darkgoldenrod">
                    <span>Impuestos (IVA 16%):</span>
                    <span class="font-semibold">$ {{ number_format($this->tax, 2) }}</span>
                </div>
                <div
                    class="flex justify-between text-lg font-extrabold pt-2 border-t border-dashed dark:border-neutral-700">
                    <span>TOTAL:</span>
                    <span class="text-xl text-indigo-700 dark:text-indigo-400">$
                        {{ number_format($this->total, 2) }}</span>
                </div>

                <button
                    class="w-full py-4 rounded-xl text-white font-bold text-lg bg-green-600 hover:bg-green-700 transition duration-150 shadow-lg disabled:bg-green-400"
                    @if (empty($cart)) disabled @endif>
                    Registrar Pedido($ {{ number_format($this->total, 2) }})
                </button>


            </div>
        </div>


    </div>
</div>

{{-- Script simple para rotar la flecha del acordeón (mejora UX) --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const details = document.querySelector('details');
        if (details) {
            const arrow = details.querySelector('.details-arrow');

            details.addEventListener('toggle', () => {
                if (details.open) {
                    arrow.style.transform = 'rotate(180deg)';
                } else {
                    arrow.style.transform = 'rotate(0deg)';
                }
            });
            // Inicializar el estado si ya está abierto
            if (details.open) {
                arrow.style.transform = 'rotate(180deg)';
            }
        }
    });
</script>
