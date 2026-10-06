<?php

namespace App\Enums;

enum ProductionStatus: string
{
    case QUEUED = 'queued';
    case APPROVED = 'approved';
    case IN_PRODUCTION = 'in_production';
    case PRINTING = 'printing';
    case QUALITY_CHECK = 'quality_check';
    case READY_FOR_PACKAGING = 'ready_for_packaging';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case REWORK = 'rework';
}
