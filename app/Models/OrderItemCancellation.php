<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItemCancellation extends Model
{
    use HasFactory;

    protected $table = 'order_item_cancellations';

    protected $fillable = [
        'order_id',
        'order_item_id',
        'user_id',
        'quantity',
        'refund_amount',
        'cancelled_at',
        'reason',
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'cancelled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}