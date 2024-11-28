<?php
namespace App\Enums;

enum PaymentTypeEnum : string
{
    case CASH = 'Cash';
    case TRANSFER = 'Transfer';
}