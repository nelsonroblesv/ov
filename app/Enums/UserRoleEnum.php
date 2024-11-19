<?php
namespace App\Enums;

enum UserRoleEnum : string
{
    case ADMINISTATOR = 'Administrador';
    case MANAGER = 'Gerente';
    case SELLER = 'Vendedor';
}