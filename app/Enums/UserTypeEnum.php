<?php
namespace App\Enums;

enum UserTypeEnum : string
{
    case ADMINISTATOR = 'Administrador';
    case MANAGER = 'Gerente';
    case SELLER = 'Vendedor';
}