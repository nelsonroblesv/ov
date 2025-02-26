<?php
namespace App\Enums;

enum OrderStatusEnum : string
{
    case PENDING = 'Pendiente';
    case COMPLETED = 'Completado';
    case PROCESSING = 'Procesando';
    case DECLINED = 'Rechazado';
    case CANCELLED = 'Cancelado';
    case PARTIAL = 'Devuelta Parcial';

}