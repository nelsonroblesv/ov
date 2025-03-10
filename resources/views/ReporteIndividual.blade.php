<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style> body { font-family: Arial, sans-serif; } </style>
</head>
<body>
    <h2>Orden #{{ $record->number }}</h2>
    <p><strong>Cliente:</strong> {{ $record->customer_id }}</p>
    <p><strong>Fecha:</strong> {{ $record->created_at }}</p>
    <p><strong>Total:</strong> ${{ number_format($record->grand_total, 2) }}</p>
    <p><strong>Tipo de Orden:</strong> {{ ucfirst($record->tipo) }}</p>

    <h3>Código QR</h3>
   
</body>
</html>