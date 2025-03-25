<?php
namespace App\Enums;

enum UserRoleEnum : string
{
    case ADMINISTRADOR = 'Administrador';
    case GERENTE = 'Gerente';
    case VENDEDOR = 'Vendedor';
    case REPARTIDOR = 'Repartidor';
    case OFICINA = 'Oficina';
}