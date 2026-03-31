<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'coupon_id',
        'payment_id',
        'order_number',
        'subtotal',
        'discount',
        'delivery_charge',
        'wallet_used',
        'paid_amount',
        'total_amount',
        'status',
        'paid_at',
        'cancelled_at',
        'price_breakdown',
        'delivered_at'
    ];

    protected $casts = [
    'price_breakdown' => 'array',
    'paid_at' => 'datetime',
    'cancelled_at' => 'datetime',
    'delivered_at' => 'datetime',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function walletTransactions()
    {
        return $this->hasMany(StoreWalletTransaction::class);
    }

    public function cancellations()
    {
        return $this->hasMany(OrderItemCancellation::class);
    }
}
