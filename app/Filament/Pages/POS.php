<?php

namespace App\Filament\Pages;

use App\Models\Customer;
use App\Models\Pedido;
use App\Models\Product;
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

        return $this->items->filter(function($item){
             return str_contains(strtolower($item->name), strtolower($this->search));
        });
    }
}
