<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class OrdenPDFController extends Controller
{
    public function __invoke(Order $order)
    {
        return Pdf::loadView('ReporteIndividual', ['record' => $order])
            ->download('OV-'.$order->number. '.pdf');
    }
}
