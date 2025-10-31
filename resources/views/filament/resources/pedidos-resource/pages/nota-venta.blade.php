<x-filament-panels::page>

    <head>
        <title>Nota de Venta {{ $customer->name }}</title>
        <link rel="stylesheet" href="{{ asset('css/customer_invoice.css') }}" type="text/css">
    </head>

    <div
        style="background-color: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 24px; max-width: 800px; margin: 0 auto;">
        <!-- Header -->
        <table class="invoice" style="width: 100%; border-bottom: 1px solid #e5e7eb; padding-bottom: 16px;">
            <tr>
                <td>
                    <h1 class='invoice' style="margin-bottom: 15px;">Nota de Venta</h1>
                    <p class='invoice'>Cliente: {{ $customer->name }}</p>
                    <p class='invoice'>Fecha: {{ now()->format('d/m/Y') }}</p>
                    <p class='invoice'>Dirección: {{ $customer->full_address }}</p>
                    <p class='invoice'>Teléfono: {{ $customer->phone }}</p>
                    <p class='invoice'>Correo: {{ $customer->email }}</p>
                </td>
                <td style="text-align: right;">
                    <img src="{{ asset('images/logo_VAMA_N.png') }}" alt="Logo VAMA" style="width: 64px;">

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

        <!-- Pedidos -->
        <h4 class="invoice">Productos</h4>
        <table class="invoice"
            style="width: 100%; margin-top: 24px; border-collapse: collapse; border: 1px solid #e2e8f0;">
            <thead>
                <tr style="background-color: #f7fafc;">
                    <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: center; color: #4a5568;">Producto
                    </th>
                    <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: center; color: #4a5568;">Cantidad
                    </th>
                    <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: center; color: #4a5568;">Precio
                    </th>
                    <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: center; color: #4a5568;">Importe
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($productos as $item)
                    <tr>
                        <td style="border: 1px solid #e2e8f0; padding: 8px; color: #718096;text-align: left">
                            {{ $item->product->name }}</td>
                        <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: left; color: #718096;text-align: center">
                            {{ $item->quantity }}</td>
                        <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; color: #718096;">
                            $ {{ number_format($item->price_publico, 2) }}</td>
                        <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; color: #718096;">
                            $ {{ number_format($item->quantity * $item->price_publico, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"
                        style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; font-weight: bold; color: #4a5568;">
                        Subtotal</td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; color: #4a5568;">$
                        {{ number_format($total, 2) }}
                    </td>
                </tr>
                <tr>
                    <td colspan="3"
                        style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; font-weight: bold; color: #4a5568;">
                        Impuesto</td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; color: #4a5568;">$
                        {{ number_format($total * 0.16, 2) }}
                    </td>
                <tr>
                    <td colspan="3"
                        style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; font-weight: bold; color: #4a5568;">
                        Total</td>
                    <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; color: #4a5568;">$
                        {{ number_format($total * 1.16, 2) }}
                    </td>
                </tr>
                </tr>
            </tfoot>
        </table>


        <footer>
            <p style="text-align: center; font-size: 0.8em; color: #718096; margin-top: 24px;">
                Este documento es un estado de cuenta y no una factura fiscal. Para cualquier consulta, por favor
                contáctenos al correo electrónico: <a href="mailto:info@empresa.com">gerencia@osberthvalle.com</a>
            </p>
        </footer>
    </div>

</x-filament-panels::page>
