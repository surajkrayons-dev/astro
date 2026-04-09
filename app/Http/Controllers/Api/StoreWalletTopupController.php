<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\StoreWallet;
use App\Models\StoreWalletTransaction;
use App\Models\Payment;

class StoreWalletTopupController extends Controller
{
    // 🔥 MANUAL CONTROL
    protected $isTest = true; // true = test | false = live

    // ✅ STEP 1: CREATE ORDER
    public function createTopupOrder(Request $request)
    {
        try {

            $user = $request->user();

            $request->validate([
                'amount' => 'required|numeric|min:1'
            ]);

            $amount = round($request->amount, 2);

            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

            $order = $api->order->create([
                'receipt' => 'wallet_' . uniqid(),
                'amount' => (int) ($amount * 100),
                'currency' => 'INR',
                'notes' => [
                    'type' => 'wallet_topup',
                    'user_id' => $user->id,
                    'amount' => $amount
                ]
            ]);

            return response()->json([
                'status' => true,
                'order_id' => $order['id'],
                'amount' => $amount
            ]);

        } catch (\Exception $e) {

            Log::error('TOPUP ORDER ERROR', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => false,
                'message' => $this->isTest ? $e->getMessage() : 'Unable to create order'
            ], 500);
        }
    }

    // ✅ STEP 2: VERIFY & CREDIT WALLET
    public function verifyTopup(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'nullable'
        ]);

        DB::beginTransaction();

        try {

            $user = $request->user();

            // ✅ DUPLICATE CHECK
            $existing = Payment::where('transaction_id', $request->razorpay_payment_id)->first();
            if ($existing) {
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'Payment already processed'
                ]);
            }

            $amount = 0;
            $paymentData = null;
            $paymentMode = 'test';

            if (!$this->isTest) {

                // 🔥 LIVE MODE (REAL RAZORPAY)
                $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

                // ✅ FETCH ORDER
                $order = $api->order->fetch($request->razorpay_order_id);
                $amount = ($order['amount'] ?? 0) / 100;

                // ✅ VERIFY SIGNATURE
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

                // 🔥 TEST MODE
                $amount = $request->amount;
                $paymentData = $request->all();
                $paymentMode = 'test';
            }

            // ✅ SAVE PAYMENT
            $payment = Payment::create([
                'user_id' => $user->id,
                'platform' => 'astrotring_store',
                'order_id' => $request->razorpay_order_id,
                'payment_gateway' => 'razorpay',
                'transaction_id' => $request->razorpay_payment_id,
                'amount' => $amount,
                'currency' => 'INR',
                'payment_status' => 'success',
                'payment_mode' => $paymentMode,
                'customer_email' => $user->email,
                'customer_phone' => trim(($user->country_code ?? '') . ($user->mobile ?? '')),
                'payment_request_data' => [
                    'type' => 'wallet_topup'
                ],
                'payment_response_data' => $paymentData
            ]);

            // ✅ WALLET UPDATE
            $wallet = StoreWallet::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'balance' => 0,
                    'total_added' => 0,
                    'total_spent' => 0,
                    'total_refunded' => 0
                ]
            );

            $before = $wallet->balance;
            $after = $before + $amount;

            $wallet->update([
                'balance' => $after,
                'total_added' => $wallet->total_added + $amount,
                'last_recharge_amount' => $amount,
                'last_recharge_at' => now()
            ]);

            // ✅ WALLET TRANSACTION
            StoreWalletTransaction::create([
                'order_id' => null,
                'payment_id' => $payment->id,
                'user_id' => $user->id,
                'type' => 'credit',
                'amount' => $amount,
                'source' => 'topup',
                'balance_before' => $before,
                'balance_after' => $after,
                'note' => 'Wallet top-up via Razorpay'
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Wallet topped up successfully',
                'wallet' => [
                    'before' => $before,
                    'added' => $amount,
                    'after' => $after,
                    'current_balance' => $after
                ]
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $this->isTest ? $e->getMessage() : 'Payment failed'
            ], 500);
        }
    }
}