<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderThankYouMail;
use App\Mail\OrderDetailsMail;
use App\Mail\OrderDeliveredMail;
use App\Mail\OrderCancelledMail;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'address_id',
        'name',
        'mobile',
        'alternative_mobile',
        'address',
        'pincode',
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

    public function addressData()
    {
        return $this->belongsTo(\App\Models\AlternativeAddress::class, 'address_id');
    }

    public function walletTransactions()
    {
        return $this->hasMany(StoreWalletTransaction::class);
    }

    public function cancellations()
    {
        return $this->hasMany(OrderItemCancellation::class);
    }

    protected static function booted()
    {
        static::created(function ($order) {

            DB::afterCommit(function () use ($order) {

                try {

                    $order = $order->fresh()->load([
                        'user',
                        'items.product', 
                        'payment'
                    ]);

                    if (!$order->user || !$order->user->email) {
                        \Log::error('User email missing');
                        return;
                    }

                    Mail::to($order->user->email)
                        ->send(new \App\Mail\OrderThankYouMail($order));

                    Mail::to($order->user->email)
                        ->send(new \App\Mail\OrderDetailsMail($order));

                } catch (\Exception $e) {
                    \Log::error('Order Mail Failed', [
                        'error' => $e->getMessage()
                    ]);
                }

            });

        });

        static::updated(function ($order) {

            if (!$order->isDirty('status')) {
                return;
            }

            DB::afterCommit(function () use ($order) {

                try {

                    $order = $order->fresh()->load([
                        'user',
                        'items.product',
                        'payment',
                        'walletTransactions'
                    ]);

                    if (!$order->user || !$order->user->email) {
                        \Log::error('User email missing');
                        return;
                    }

                    // ✅ DELIVERED
                    if (
                        $order->getOriginal('status') != 'delivered' &&
                        $order->status == 'delivered'
                    ) {
                        Mail::to($order->user->email)
                            ->send(new \App\Mail\OrderDeliveredMail($order));

                        \Log::info('Delivered mail sent', ['order_id' => $order->id]);
                    }

                    // ✅ CANCELLED
                    if (
                        $order->getOriginal('status') != 'cancelled' &&
                        $order->status == 'cancelled'
                    ) {
                        Mail::to($order->user->email)
                            ->send(new \App\Mail\OrderCancelledMail($order));

                        \Log::info('Cancel mail sent', ['order_id' => $order->id]);
                    }

                } catch (\Exception $e) {
                    \Log::error('Order Mail Failed', [
                        'error' => $e->getMessage()
                    ]);
                }

            });
        });
    }
}