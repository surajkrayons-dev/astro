<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreWallet extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'store_wallets';

    protected $fillable = [
        'user_id',
        'balance',
        'total_added',
        'total_spent',
        'total_refunded',
        'last_recharge_amount',
        'last_recharge_at',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'total_added' => 'decimal:2',
        'total_spent' => 'decimal:2',
        'total_refunded' => 'decimal:2',
        'last_recharge_amount' => 'decimal:2',
        'last_recharge_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
