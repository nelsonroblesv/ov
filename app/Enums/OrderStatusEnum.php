<?php
namespace App\Enums;

enum OrderStatusEnum : string
{
    case PENDING = 'Pendiente';
    case COMPLETED = 'Completada';
    case PROCESSING = 'Procesando';
    case DECLINED = 'Rechazada';
}