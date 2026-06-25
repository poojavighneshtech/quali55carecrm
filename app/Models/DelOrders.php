<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DelOrders extends Model
{
    use HasFactory;

    protected $table = 'del_orders';
    
    protected $fillable = [
        'order_id',
        'lead_id',
        'vendor_id',
        'order_number',
        'order_date',
        'status',
        'deliverypickup',
        'shipping_total',
        'order_total',
        'payment_method',
        'customer_email',
        'shipping_first_name',
        'shipping_last_name',
        'cust_gender',
        'shipping_company',
        'shipping_address_1',
        'shipping_address_2',
        'shipping_postcode',
        'shipping_city',
        'location',
        'shipping_state',
        'shipping_country',
        'mobileno',
        'customer_note',
        'line_item_1',
        'line_item_2',
        'DelDate',
        'DelAssignedTo',
        'helpers',
        'ReceiptToBeCarried',
        'TotalAmt',
        'cash',
        'online',
        'PaymentMode',
        'Delivery_Date',
        'Pickup_Date',
        'Collection_Date',
        'TravelMode',
        'TimeSlot',
        'fulldetails',
        'PickupLocation',
        'itemAddress',
        'DropLocation',
        'custstarrating',
        'custcomments',
        'custdisclaimer',
        'updatedDateTime',
        'updatedBy',
        'updated_at',
        'cust_sign',
        'product_delivered',
        'payment_image',
        'reference_id',
        'order_approval_status',
        'cancellation_reason',
        'delivery_otp',
        'otp_verified',
        'del_payment_mode',
        'del_total_amount',
        'del_cash_amount',
        'del_online_amount',
        'del_receipt_image',
        'comment',
        'settlement_status',
        'floor_wise_labour_charges',
        'floor_no',
        'labour_charges',
        'isUpgraded'
    ];
}
