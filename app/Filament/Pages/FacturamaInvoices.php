<?php

namespace App\Filament\Pages;

use App\Services\FacturamaService;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class FacturamaInvoices extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.facturama-invoices';
    protected static ?string $title = 'CFDIS Facturama (API)';

    public Collection $invoices;

    public string $search = '';

    public function mount(FacturamaService $facturama)
    {
        $this->invoices = new Collection();

        $apiResponse = $facturama->getInvoices();

        if ($apiResponse instanceof Collection) {
            $apiResponse = $apiResponse->first();
        }

        // 1. Obtén la respuesta y decodifícala (como lo haces ahora)
        $apiArray = json_decode(json_encode($apiResponse), true);

        // 2. CORRECCIÓN: Asegúrate de tomar el array que contiene las facturas.
        //    Si tu array original es array:1 [▼ 0 => array:15 [ ... factura ... ] ],
        //    entonces el array de facturas está en el índice [0].
        $invoicesList = $apiArray[0] ?? $apiArray;

        // 3. Convierte el array de facturas en una colección.
        //    Para mayor seguridad, si solo te devuelve una factura (como en tu ejemplo), 
        //    deberías asegurarte de que $this->invoices sea un array de arrays.
        if (!isset($invoicesList['Id'])) {
            // Si $invoicesList es un array que contiene multiples facturas:
            $this->invoices = collect($invoicesList);
        } else {
            // Si $invoicesList es solo UNA factura, forzamos a que sea un array de un solo elemento
            $this->invoices = collect([$invoicesList]);
        }

        // dd($this->invoices);
    }
}
