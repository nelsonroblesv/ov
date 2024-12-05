<?php
namespace App\Enums;

enum CfdiTypeEnum : string
{
    case NINGUNO = 'Nignuno';
    case INGRESO = 'Ingreso';
    case EGRESO = 'Egreso';
    case TRASLADO = 'Traslado';
    case NOMINA = 'Nomina';
}