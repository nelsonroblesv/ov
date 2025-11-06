<x-filament-panels::page>

    <head>
        <title>Nota de Venta</title>
        <link rel="stylesheet" href="{{ asset('css/nota_venta.css') }}" type="text/css">
    </head>

    <div
        style="background-color: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 24px; max-width: 800px; margin: 0 auto;">

        <table class="encabezado">
            <tr>
                <td style="width:25%">
                    <div><img src="{{ asset('images/logo_VAMA_N.png') }}" alt="Logo VAMA" style="width: 64px;"></div>
                </td>
                <td style="width:50%">
                    <h1>OSBERTH NICOLAS VALLE DE ATOCHA PINZON</h1>
                    <p>ID: 0123456789</p>
                    <P>correo@servidor.com</P>
                </td>
                <td style="width:25%;vertical-align:top;">
                    <span><b>Folio:</b> {{ $pedido->id_nota }}</span>
                </td>
            </tr>
        </table>


        <div
            style="background-color: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 24px; max-width: 800px; margin: 0 auto;">

            <table class="datos" style="width: 100%; border-bottom: 1px solid #e5e7eb; padding-bottom: 16px;">
                <tr>
                    <td>
                        <p><b>Cliente:</b> </p>
                    </td>
                    <td>
                        <p><u>{{ $customer->name }}</u></p>
                    </td>
                    <td>
                        <p><b>Fecha de emision:</b></p>
                    </td>
                    <td>
                        <p>24 sep, 2025</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p><b>RFC:</b></p>
                    </td>
                    <td>
                        <p>ROVN880320</p>
                    </td>
                    <td>
                        <p><b>Vencimiento:</b></p>
                    </td>
                    <td>
                        <p>31 oct, 2025</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p><b>Regimen Fiscal:</b></p>
                    </td>
                    <td>
                        <p>Servicios Profesionales</p>
                    </td>
                    <td>
                        <p><b>Plazo de pago:</b></p>
                    </td>
                    <td>
                        <p>Contado</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p><b>Codigo Postal:</b></p>
                    </td>
                    <td>
                        <p>24800</p>
                    </td>
                    <td>
                        <p></p>
                    </td>
                    <td>
                        <p></p>
                    </td>
                </tr>
            </table>


            <!-- Pedidos -->
            <h4 class="">Pedido</h4>
            <table class="pedido">
                <thead>
                    <tr style="background-color: #f7fafc;">
                        <th>
                            <p style="text-align: left;">Producto</p>
                        </th>
                        <th>
                            <p> Cantidad</p>
                        </th>
                        <th>
                            <p style="text-align: right;">Precio</p>
                        </th>
                        <th>
                            <p style="text-align: right;">Importe</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productos as $item)
                        <tr>
                            <td style="padding: 8px; color: #718096;text-align: left">
                                {{ $item->product->name }}</td>
                            <td style="padding: 8px; text-align: left; color: #718096;text-align: center">
                                {{ $item->quantity }}</td>
                            <td style="padding: 8px; text-align: right; color: #718096;">
                                $ {{ number_format($item->price_publico, 2) }}</td>
                            <td style="padding: 8px; text-align: right; color: #718096;">
                                $ {{ number_format($item->quantity * $item->price_publico, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="padding: 8px; text-align: right; font-weight: bold; color: #4a5568;">
                            Subtotal</td>
                        <td style="padding: 8px; text-align: right; color: #4a5568;">$
                            {{ number_format($total, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="paddin: 8px; text-align: right; font-weight: bold; color: #4a5568;">
                            Impuesto</td>
                        <td style="padding: 8px; text-align: right; color: #4a5568;">$
                            {{ number_format($total * 0.16, 2) }}
                        </td>
                    <tr>
                        <td colspan="3" style="padding: 8px; text-align: right; font-weight: bold; color: #4a5568;">
                            Total</td>
                        <td style="padding: 8px; text-align: right; color: #4a5568;">$
                            {{ number_format($total * 1.16, 2) }}
                        </td>
                    </tr>
                    </tr>
                </tfoot>
            </table>

            <h4 class="">Informacion adicional</h4>
            <table class="notas">
                <tr>
                    <td>
                        <p>Método de pago</p>
                    </td>
                    <td>
                        <p><b>Pago en una sola exhibicion</b></p>n
                    </td>
                </tr>
                <tr>
                    <td>
                        <p>Forma de Pago</p>
                    </td>
                    <td>
                        <p><b>Transferencia bancaria</b></p>a
                    </td>
                </tr>
                <tr>
                    <td>
                        <p>Uso</p>
                    </td>
                    <td>
                        <p><b>Sin efectos fiscales</b></p>
                    </td>
                </tr>
            </table>

            <table class="declaracion">
                <tr>
                    <td style="width: 70%">
                        <p><b>Declaración de conformidad y compromiso de pago</b></p>
                    </td>
                    <td>
                        <p style="text-align: center"><b>Firma del Cliente</b></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p>Por medio de la presente, confirmo haber recibido a mi entera satisfacción los productos
                            descritos en esta nota de venta. Me comprometo formalmente a liquidar el total pendiente en
                            los términos acordados con el vendedor. </p>
                    </td>
                    <td>
                        @if ($pedido->is_signed)
                            <span>
                                <img src="{{ $pedido->signature }}">
                            </span>
                        @endif
                    </td>
                </tr>
            </table>


            <footer>
                <p style="text-align: center; font-size: 0.8em; color: #718096; margin-top: 24px;">
                    Para cualquier consulta o aclaracion, por favor
                    contáctenos al correo electrónico: <a href="mailto:info@empresa.com">gerencia@survama.com</a>
                </p>
            </footer>
        </div>

</x-filament-panels::page>
