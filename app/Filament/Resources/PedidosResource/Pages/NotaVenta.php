<?php

namespace App\Filament\Resources\PedidosResource\Pages;

use App\Filament\Resources\PedidosResource;
use App\Models\Customer;
use App\Models\Pedido;
use App\Models\PedidosItems;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\Page;

class NotaVenta extends Page
{
    protected static string $resource = PedidosResource::class;
    protected static string $view = 'filament.resources.pedidos-resource.pages.nota-venta';

    public $record;
    public $customer;
    public $pedido;
    public $productos;
    public $catalogo;
    public $total;

    public function mount($record): void
    {
        $this->record = $record;

        $this->pedido = Pedido::with('items.product')->find($record);
        $this->customer = Customer::find($this->pedido->customer_id);

        $this->productos = $this->pedido->items;

        $total = array_sum(array_column($this->productos->toArray(), 'total_price'));
        $this->total = $total;
    }

    protected function getHeaderActions(): array
    {
        return [

            Action::make('edit')
                ->label('Editar Pedido')
                ->icon('heroicon-o-pencil-square')
                ->color('warning')
                ->url(PedidosResource::getUrl('edit', ['record' => $this->record])),

            Action::make('print')
                ->label('Imprimir')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->requiresConfirmation()
                ->url(route('PRINT.NOTA_VENTA', ['id'=>$this->record])),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
