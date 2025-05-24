 <head>
     <title>Estado de Cuenta {{ $customer->name }}</title>
 </head>
 <div
     style="background-color: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 24px; max-width: 800px; margin: 0 auto;">
     <!-- Header -->
     <table style="width: 100%; border-bottom: 1px solid #e5e7eb; padding-bottom: 16px;">
         <tr>
             <td>
                 <h1 style="font-size: 24px; font-weight: bold; color: #4a5568;">Estado de Cuenta</h1>
                 <p style="color: #a0aec0;">Cliente: {{ $customer->name }}</p>
                 <p style="color: #a0aec0;">Fecha: {{ now()->format('d/m/Y') }}</p>
             </td>
             <td style="text-align: right;">
                 <img src="https://app.osberthvalle.com/images/logo_ovalleB.png" alt="Logo OV" style="width: 64px;">
             </td>
         </tr>
     </table>

     <!-- Facturacion -->
     <table style="width: 100%; margin-top: 24px;">
         <tr>
             <td>
                 <h2 style="font-weight: bold; color: #4a5568;">Datos del Cliente</h2>
                 <p style="color: #718096;"> {{ $customer->full_address }}</p>
                 <p style="color: #718096;"> {{ $customer->phone }}</p>
                 <p style="color: #718096;"> {{ $customer->email }}</p>
             </td>
         </tr>

         <!--tr>
                <td style="text-align: right;">
                    <h2 style="font-weight: bold; color: #4a5568;">Osberth Valle</h2>
                    <p style="color: #718096;">Current Company</p>
                    <p style="color: #718096;">Current Address</p>
                    <p style="color: #718096;">Current Zipcode</p>
                    <p style="color: #718096;">Email: Current Email</p>
                </td>
            </tr-->
     </table>

     <!-- Orders -->
     <h2 style="font-weight: bold; color: #4a5568;">Pedidos</h2>
     <table style="width: 100%; margin-top: 24px; border-collapse: collapse; border: 1px solid #e2e8f0;">
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
     <h2 style="font-weight: bold; color: #4a5568;">Pagos</h2>
     <table style="width: 100%; margin-top: 24px; border-collapse: collapse; border: 1px solid #e2e8f0;">
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
                     style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; font-weight: bold; color: #4a5568;">
                     Total</td>
                 <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; color: #4a5568;">
                     $ {{ number_format($payment->sum('importe'), 2) }}</td>
             </tr>
         </tfoot>
     </table>

     <table style="width: 100%; margin-top: 24px; border-collapse: collapse; border: 1px solid #e2e8f0;">
         <thead>
             <tr style="background-color: #f7fafc;">
                 <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: left; color: #4a5568;">Saldo
                 </th>
                 <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; color: #4a5568;font-size:1.5em">
                     @if ($order->sum('grand_total') - $payment->sum('importe') < 0)
                         $ {{ number_format($order->sum('grand_total') - $payment->sum('importe'), 2) }} (A favor)
                     @else
                         $ {{ number_format($order->sum('grand_total') - $payment->sum('importe'), 2) }}
                     @endif
                 </th>
             </tr>
         </thead>
     </table>
 </div>
