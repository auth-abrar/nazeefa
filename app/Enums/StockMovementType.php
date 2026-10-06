<?php

namespace App\Enums;

enum StockMovementType: string
{
    case PURCHASE = 'purchase';
    case SALE = 'sale';
    case RESERVATION = 'reservation';
    case RELEASE = 'release';
    case ADJUSTMENT = 'adjustment';
    case RETURN = 'return';
    case DAMAGE = 'damage';
    case PRODUCTION_CONSUMPTION = 'production_consumption';
    case PRODUCTION_OUTPUT = 'production_output';
    case TRANSFER = 'transfer';
}
