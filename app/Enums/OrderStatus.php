<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING_PAYMENT = 'pending_payment';
    case PAYMENT_REVIEW = 'payment_review';
    case CONFIRMED = 'confirmed';
    case PROCESSING = 'processing';
    case AWAITING_CUSTOM_APPROVAL = 'awaiting_custom_approval';
    case READY_FOR_PRODUCTION = 'ready_for_production';
    case IN_PRODUCTION = 'in_production';
    case QUALITY_CHECK = 'quality_check';
    case PACKED = 'packed';
    case READY_TO_SHIP = 'ready_to_ship';
    case SHIPPED = 'shipped';
    case OUT_FOR_DELIVERY = 'out_for_delivery';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
    case RETURN_REQUESTED = 'return_requested';
    case RETURNED = 'returned';
    case REFUNDED = 'refunded';
    case PARTIALLY_REFUNDED = 'partially_refunded';
    case FAILED_DELIVERY = 'failed_delivery';

    public function label(): string
    {
        return match ($this) {
            self::PENDING_PAYMENT => 'Pending Payment',
            self::PAYMENT_REVIEW => 'Payment Under Review',
            self::CONFIRMED => 'Order Confirmed',
            self::PROCESSING => 'Processing',
            self::AWAITING_CUSTOM_APPROVAL => 'Awaiting Proof Approval',
            self::READY_FOR_PRODUCTION => 'Ready for Production',
            self::IN_PRODUCTION => 'In Production',
            self::QUALITY_CHECK => 'Quality Inspection',
            self::PACKED => 'Packed',
            self::READY_TO_SHIP => 'Ready to Dispatch',
            self::SHIPPED => 'Shipped',
            self::OUT_FOR_DELIVERY => 'Out for Delivery',
            self::DELIVERED => 'Delivered',
            self::CANCELLED => 'Cancelled',
            self::RETURN_REQUESTED => 'Return Requested',
            self::RETURNED => 'Returned',
            self::REFUNDED => 'Refunded',
            self::PARTIALLY_REFUNDED => 'Partially Refunded',
            self::FAILED_DELIVERY => 'Delivery Failed',
        };
    }
}
