<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreWalletTransaction extends Model
{
    use HasFactory;

    protected $table = 'store_wallet_transactions';

    protected $fillable = [
        'user_id',
        'order_id',
        'type',
        'amount',
        'source',
        'balance_before',
        'balance_after',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}