<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreWallet;
use App\Models\StoreWalletTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreWalletApiController extends Controller
{
    public function show()
    {
        $wallet = StoreWallet::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'balance' => 0,
                'total_added' => 0,
                'total_spent' => 0,
                'total_refunded' => 0
            ]
        );

        return response()->json([
            'success' => true,
            'data' => [
                'balance' => (int) $wallet->balance,
                'total_added' => (int) $wallet->total_added,
                'total_spent' => (int) $wallet->total_spent,
                'total_refunded' => (int) $wallet->total_refunded,
                'last_recharge_amount' => (int) $wallet->last_recharge_amount,
            ]
        ]);
    }

    public function history(Request $request)
    {
        $query = StoreWalletTransaction::with('order')
            ->where('user_id', Auth::id());

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->source) {
            $query->where('source', $request->source); 
        }

        $transactions = $query->latest()->paginate(10);

        $transactions->getCollection()->transform(function ($item) {
            $item->amount = (int) $item->amount;
            $item->balance_before = (int) $item->balance_before;
            $item->balance_after = (int) $item->balance_after;
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    public function summary()
    {
        $wallet = StoreWallet::firstOrCreate(['user_id' => Auth::id()]);

        return response()->json([
            'success' => true,
            'data' => [
                'balance' => (int) ($wallet->balance ?? 0),
                'total_added' => (int) ($wallet->total_added ?? 0),
                'total_spent' => (int) ($wallet->total_spent ?? 0),
                'total_refunded' => (int) ($wallet->total_refunded ?? 0),
            ]
        ]);
    }

    public function spendHistory(Request $request)
    {
        $transactions = StoreWalletTransaction::with('order')
            ->where('user_id', Auth::id())
            ->where('type', 'debit') 
            ->where('source', 'order_payment')
            ->latest()
            ->paginate(10);

        $transactions->getCollection()->transform(function ($item) {
            $item->amount = (int) $item->amount;
            $item->balance_before = (int) $item->balance_before;
            $item->balance_after = (int) $item->balance_after;
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }
}