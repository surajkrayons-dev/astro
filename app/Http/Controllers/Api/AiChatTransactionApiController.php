<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiChatTransaction;
use Illuminate\Http\Request;

class AiChatTransactionApiController extends Controller
{
    public function index(Request $request)
    {
        $transactions = AiChatTransaction::with([
            'session.topic',
            'message'
        ])
        ->where('user_id', $request->user()->id)
        ->latest()
        ->get();

        return response()->json([
            'status' => true,
            'data' => $transactions
        ]);
    }
}