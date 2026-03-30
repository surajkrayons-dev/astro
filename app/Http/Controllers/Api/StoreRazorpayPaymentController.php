<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Payment;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\StoreWallet;

class StoreRazorpayPaymentController extends Controller
{
    protected $isTest = true; // true = test | false = live

    public function createOrder(Request $request)
    {
        try {

            $user = $request->user();

            $request->validate([
                'coupon_code' => 'nullable|string',
                'wallet_amount' => 'nullable|numeric|min:0',
                'delivery_charge' => 'nullable|numeric|min:0'
            ]);

            $walletInput = $request->wallet_amount ?? 0;
            $deliveryCharge = $request->delivery_charge ?? 0;

            // 🔥 CART
            $cart = Cart::where('user_id', $user->id)->firstOrFail();
            $items = CartItem::where('cart_id', $cart->id)->get();

            if ($items->isEmpty()) {
                return response()->json(['status' => false, 'message' => 'Cart empty']);
            }

            $subtotal = $items->sum('total_price');

            // 🔥 COUPON (ONLY IF SENT)
            $discount = 0;

            if ($request->coupon_code) {

                $coupon = Coupon::where('code', $request->coupon_code)
                    ->where('status', 1)
                    ->whereDate('expiry_date', '>=', now())
                    ->first();

                if ($coupon) {

                    if ($coupon->min_amount && $subtotal < $coupon->min_amount) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Coupon min amount not met'
                        ]);
                    }

                    if ($coupon->discount_type == 'flat') {
                        $discount = $coupon->discount_value;
                    } else {
                        $discount = ($subtotal * $coupon->discount_value) / 100;

                        if ($coupon->max_discount) {
                            $discount = min($discount, $coupon->max_discount);
                        }
                    }
                }
            }

            $afterDiscount = max(0, $subtotal - $discount);

            // 🔥 WALLET VALIDATION (USER CONTROLLED)
            $wallet = StoreWallet::firstOrCreate(['user_id' => $user->id]);

            if ($walletInput > $wallet->balance) {
                return response()->json([
                    'status' => false,
                    'message' => 'Insufficient wallet balance'
                ]);
            }

            if ($walletInput > ($afterDiscount + $deliveryCharge)) {
                $walletInput = ($afterDiscount + $deliveryCharge);
            }

            $walletUsed = $walletInput;
            $finalAmount = max(0, ($afterDiscount + $deliveryCharge) - $walletUsed);

            // 🔥 RAZORPAY
            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

            $order = $api->order->create([
                'receipt' => 'store_' . uniqid(),
                'amount' => (int) round($finalAmount * 100),
                'currency' => 'INR',

                'notes' => [
                    'user_id' => $user->id,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'delivery_charge' => $deliveryCharge,
                    'wallet_used' => $walletUsed,
                    'final_amount' => $finalAmount
                ]
            ]);

            return response()->json([
                'status' => true,
                'order_id' => $order['id'],
                'breakdown' => [
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'delivery_charge' => $deliveryCharge,
                    'wallet_used' => $walletUsed,
                    'final_amount' => $finalAmount
                ]
            ]);

        } catch (\Exception $e) {

            Log::error('STORE CREATE ORDER ERROR', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'message' => $this->isTest ? $e->getMessage() : 'Unable to create order'
            ], 500);
        }
    }
 
    public function verify(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'nullable',
            'razorpay_signature' => 'nullable',
            'coupon_code' => 'nullable',
            'wallet_amount' => 'nullable|numeric|min:0',
            'delivery_charge' => 'nullable|numeric|min:0'
        ]);

        DB::beginTransaction();

        try {

            $user = $request->user();
            $walletInput = $request->wallet_amount ?? 0;
            $deliveryCharge = $request->delivery_charge ?? 0;

            // 🔥 CART
            $cart = Cart::where('user_id', $user->id)->firstOrFail();
            $items = CartItem::where('cart_id', $cart->id)->get();

            if ($items->isEmpty()) {
                throw new \Exception('Cart empty');
            }

            $subtotal = $items->sum('total_price');

            // 🔥 COUPON
            $discount = 0;
            $couponId = null;

            if ($request->coupon_code) {

                $coupon = Coupon::where('code', $request->coupon_code)
                    ->where('status', 1)
                    ->whereDate('expiry_date', '>=', now())
                    ->first();

                if ($coupon) {

                    if ($coupon->min_amount && $subtotal < $coupon->min_amount) {
                        throw new \Exception('Coupon min amount not met');
                    }

                    if ($coupon->discount_type == 'flat') {
                        $discount = $coupon->discount_value;
                    } else {
                        $discount = ($subtotal * $coupon->discount_value) / 100;

                        if ($coupon->max_discount) {
                            $discount = min($discount, $coupon->max_discount);
                        }
                    }

                    $couponId = $coupon->id;
                }
            }

            $afterDiscount = max(0, $subtotal - $discount);

            // 🔥 WALLET VALIDATION
            $wallet = StoreWallet::firstOrCreate(['user_id' => $user->id]);

            if ($walletInput > $wallet->balance) {
                throw new \Exception('Invalid wallet usage');
            }

            if ($walletInput > ($afterDiscount + $deliveryCharge)) {
                $walletInput = ($afterDiscount + $deliveryCharge);
            }

            $walletUsed = $walletInput;
            $finalAmount = max(0, ($afterDiscount + $deliveryCharge) - $walletUsed);

            // 🔥 PAYMENT
            $payment = null;
            $paymentData = null;
            $paymentMode = 'wallet_only';

            if ($finalAmount > 0) {

                $existing = Payment::where('transaction_id', $request->razorpay_payment_id)->first();
                if ($existing) {
                    DB::commit();
                    return response()->json(['status' => true, 'message' => 'Already processed']);
                }

                if (!$this->isTest) {

                    $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

                    $api->utility->verifyPaymentSignature([
                        'razorpay_order_id' => $request->razorpay_order_id,
                        'razorpay_payment_id' => $request->razorpay_payment_id,
                        'razorpay_signature' => $request->razorpay_signature
                    ]);

                    $paymentData = $api->payment->fetch($request->razorpay_payment_id);

                    if (($paymentData['status'] ?? '') !== 'captured') {
                        throw new \Exception('Payment not captured');
                    }

                    $paymentMode = $paymentData['method'] ?? 'online';

                } else {
                    $paymentData = $request->all();
                    $paymentMode = 'test';
                }

                $payment = Payment::create([
                    'user_id' => $user->id,
                    'platform' => 'astrotring_store',
                    'order_id' => $request->razorpay_order_id,
                    'payment_gateway' => 'razorpay',
                    'transaction_id' => $request->razorpay_payment_id,
                    'amount' => $finalAmount,
                    'currency' => 'INR',
                    'payment_status' => 'success',
                    'payment_mode' => $paymentMode,

                    'customer_email' => $user->email,
                    'customer_phone' => trim(($user->country_code ?? '') . ($user->mobile ?? '')),

                    'payment_request_data' => [
                        'subtotal' => $subtotal,
                        'discount' => $discount,
                        'wallet_requested' => $request->wallet_amount,
                        'wallet_used' => $walletUsed,
                        'final_amount' => $finalAmount,
                        'delivery_charge' => $deliveryCharge,
                        'coupon_code' => $request->coupon_code
                    ],

                    'payment_response_data' => $paymentData
                ]);
            }

            // 🔥 WALLET DEDUCT (SAFE)
            if ($walletUsed > 0) {

                $wallet->refresh();

                if ($wallet->balance < $walletUsed) {
                    throw new \Exception('Wallet changed, retry');
                }

                $wallet->update([
                    'balance' => $wallet->balance - $walletUsed,
                    'total_spent' => $wallet->total_spent + $walletUsed
                ]);
            }

            // 🔥 ORDER CREATE
            $order = Order::create([
                'user_id' => $user->id,
                'coupon_id' => $couponId,
                'payment_id' => $payment?->id,
                'order_number' => 'ORD-' . strtoupper(uniqid()),

                'subtotal' => $subtotal,
                'discount' => $discount,
                'wallet_used' => $walletUsed,
                'delivery_charge' => $deliveryCharge,
                'paid_amount' => $finalAmount,
                'total_amount' => ($afterDiscount + $deliveryCharge),

                'price_breakdown' => [
                    'subtotal' => $subtotal,
                    'coupon_discount' => $discount,
                    'delivery_charge' => $deliveryCharge,
                    'wallet_used' => $walletUsed,
                    'final_payable' => $finalAmount
                ],

                'status' => 'paid',
                'paid_at' => now()
            ]);

            // 🔥 ORDER ITEMS
            foreach ($items as $item) {

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name ?? '',
                    'product_slug' => $item->product->slug ?? '',
                    'product_image' => $item->product->image ?? '',
                    'ratti' => $item->ratti,
                    'quantity' => $item->quantity,
                    'price' => $item->price_at_time,
                    'total' => $item->total_price
                ]);
            }

            CartItem::where('cart_id', $cart->id)->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Order placed successfully',
                'pricing' => [
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'wallet_used' => $walletUsed,
                    'delivery_charge' => $deliveryCharge,
                    'paid_online' => $finalAmount
                ]
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('STORE PAYMENT ERROR', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'message' => $this->isTest ? $e->getMessage() : 'Payment failed'
            ], 500);
        }
    }

    public function cancelOrder($id)
    {
        DB::beginTransaction();

        try {

            $user = auth()->user();

            $order = Order::where('id', $id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            // ❌ ALREADY CANCELLED
            if ($order->status == 'cancelled') {
                return response()->json([
                    'status' => false,
                    'message' => 'Order already cancelled'
                ]);
            }

            // ❌ NOT ALLOWED
            if (in_array($order->status, ['shipped', 'delivered'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order cannot be cancelled now'
                ]);
            }

            // 🔥 TOTAL REFUND (IMPORTANT)
            $refundAmount = $order->total_amount;

            // 🔥 WALLET FETCH
            $wallet = StoreWallet::firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0, 'total_added' => 0, 'total_spent' => 0]
            );

            $before = $wallet->balance;
            $after = $before + $refundAmount;

            // 🔥 UPDATE WALLET
            $wallet->update([
                'balance' => $after,
                'total_added' => $wallet->total_added + $refundAmount
            ]);

            if ($order->payment_id) {
                Payment::where('id', $order->payment_id)
                    ->update(['payment_status' => 'refunded']);
            }

            // 🔥 ORDER UPDATE
            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Order cancelled & amount refunded to wallet',
                'refund' => [
                    'amount' => $refundAmount,
                    'wallet_before' => $before,
                    'wallet_after' => $after
                ]
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}