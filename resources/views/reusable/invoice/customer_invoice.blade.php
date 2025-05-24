<head>
    <title>Estado de Cuenta {{ $customer->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/customer_invoice.css') }}" type="text/css">
</head>

<div
    style="background-color: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 24px; max-width: 800px; margin: 0 auto;">
    <!-- Header -->
    <table style="width: 100%; border-bottom: 1px solid #e5e7eb; padding-bottom: 16px;">
        <tr>
            <td>
                <h1 class='invoice'>Estado de Cuenta</h1>
                <p class='customer'>{{ $customer->name }}</p>
                <p class='invoice'> {{ now()->format('d/m/Y') }}</p>
                <p class='invoice'> {{ $customer->full_address }}</p>
                <p class='invoice'> {{ $customer->phone }}</p>
                <p class='invoice'> {{ $customer->email }}</p>
            </td>
            <td style="text-align: right;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo OV" style="width: 64px;">

            </td>
        </tr>
    </table>

    <!-- Facturacion -->
    <!--table style="width: 100%; margin-top: 24px;">
       
        <tr>
                <td style="text-align: right;">
                    <h2 style="font-weight: bold; color: #4a5568;">Osberth Valle</h2>
                    <p style="color: #718096;">Current Company</p>
                    <p style="color: #718096;">Current Address</p>
                    <p style="color: #718096;">Current Zipcode</p>
                    <p style="color: #718096;">Email: Current Email</p>
                </td>
            </tr>
    </table-->

    <!-- Orders -->
    <h4 class="invoice">Pedidos</h4>
    <table class="invoice" style="width: 100%; margin-top: 24px; border-collapse: collapse; border: 1px solid #e2e8f0;">
        <thead>
            <tr style="background-color: #f7fafc;">
                <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: left; color: #4a5568;">No. de Pedido
                </th>
                <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; color: #4a5568;">Fecha
                </th>
                <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; color: #4a5568;">Importe
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order as $item)
                <tr>
                    <td style="border: 1px solid #e2e8f0; padding: 8px; color: #718096;">
                        {{ $item->number }}</td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; color: #718096;">
                        {{ date_format($item->created_at, 'd/m/Y') }}</td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; color: #718096;">
                        $ {{ $item->grand_total }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2"
                    style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; font-weight: bold; color: #4a5568;">
                    Total</td>
                <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; color: #4a5568;">$
                    {{ number_format($order->sum('grand_total'), 2) }}
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Payments -->
    <br>
    <h4 class="invoice">Pagos</h4>
    <table class="invoice" style="width: 100%; margin-top: 24px; border-collapse: collapse; border: 1px solid #e2e8f0;">
        <thead>
            <tr style="background-color: #f7fafc;">
                <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: center; color: #4a5568;">Fecha
                </th>
                <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: center; color: #4a5568;">Tipo de Pago
                </th>
                <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; color: #4a5568;">Importe
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payment as $item)
                <tr>
                    <td style="border: 1px solid #e2e8f0; padding: 8px; color: #718096;text-align:center">
                        {{ date_format($item->created_at, 'd/m/Y') }}</td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: center; color: #718096;">
                        @if ($item->tipo == 'E')
                            Efectivo
                        @elseif ($item->tipo == 'T')
                            Transferencia
                        @else
                            Otro
                        @endif
                    </td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; color: #718096;">$
                        {{ number_format($item->importe, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2"
                    style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; font-weight: bold;">
                    Total</td>
                <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: right;">
                    $ {{ number_format($payment->sum('importe'), 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <table class="invoice" style="width: 100%; margin-top: 24px; border-collapse: collapse; border: 1px solid #e2e8f0;">
        <thead>
            <tr>
                <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: left;background:#f7fafc">Saldo
                </th>
                <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: right;">
                    @if ($order->sum('grand_total') - $payment->sum('importe') < 0)
                        $ {{ number_format($order->sum('grand_total') - $payment->sum('importe'), 2) }} (A favor)
                    @else
                        $ {{ number_format($order->sum('grand_total') - $payment->sum('importe'), 2) }}
                    @endif
                </th>
            </tr>
        </thead>
    </table>

    <footer>
        <p style="text-align: center; font-size: 0.8em; color: #718096; margin-top: 24px;">
            Este documento es un estado de cuenta y no una factura fiscal. Para cualquier consulta, por favor contáctenos al correo electrónico: <a href="mailto:info@empresa.com">gerencia@osberthvalle.com</a>
        </p>
    </footer>
</div>
