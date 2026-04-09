<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\SmartfloService;
use App\Models\SmartfloToken;
use App\Models\User;
use App\Models\Wallet;
use App\Models\CallSession;
use Carbon\Carbon;

class CallApiController extends Controller
{
    public function start(Request $request)
    {
        $user = auth()->user();
        $astro = User::findOrFail($request->astrologer_id);

        // ❌ only user allowed
        if ($user->type !== 'user') {
            return response()->json(['message' => 'Only user can call'], 403);
        }

        if (!$astro->is_online) {
            return response()->json(['message' => 'Astro offline'], 400);
        }

        if ($astro->is_busy) {
            return response()->json(['message' => 'Astro busy'], 400);
        }

        $wallet = Wallet::where('user_id', $user->id)->first();

        if ($wallet->balance < $astro->call_price) {
            return response()->json(['message' => 'Low balance'], 400);
        }

        // 🔒 lock 1 min
        $lockAmount = $astro->call_price;

        $wallet->balance -= $lockAmount;
        $wallet->locked_balance += $lockAmount;
        $wallet->save();

        /* $smartflo = new SmartfloService();
        $token = $smartflo->getToken(); */

        $token = SmartfloToken::latest()->first()?->access_token;

        if (!$token) {
            return response()->json([
                'message' => 'Smartflo token missing'
            ], 500);
        }

        $agentNumber = ltrim($astro->country_code, '+') . $astro->mobile;
        $customerNumber = ltrim($user->country_code, '+') . $user->mobile;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json'
        ])->post('https://api-smartflo.tatateleservices.com/v1/click_to_call', [
            'agent_number' => $agentNumber,
            'customer_number' => $customerNumber,
        ]);

        if (!$response->successful()) {

            $wallet->balance += $lockAmount;
            $wallet->locked_balance -= $lockAmount;
            $wallet->save();

            return response()->json([
                'message' => 'Call failed',
                'error' => $response->body()
            ], 500);
        }

        $callSid = $response->json()['call_id'] ?? null;

        if (!$callSid) {

            $wallet->balance += $lockAmount;
            $wallet->locked_balance -= $lockAmount;
            $wallet->save();

            return response()->json([
                'message' => 'Call not initiated properly',
                'response' => $response->json()
            ], 500);
        }

        $session = CallSession::create([
            'user_id' => $user->id,
            'astrologer_id' => $astro->id,
            'status' => 'pending',
            'call_sid' => $callSid,
        ]);

        return response()->json([
            'message' => 'Calling...',
            'session_id' => $session->id
        ]);
    }

    public function webhook(Request $request)
    {
        if ($request->header('x-api-key') !== env('SMARTFLO_WEBHOOK_KEY')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $callSid = $request->call_id;
        $status = $request->status;

        $session = CallSession::where('call_sid', $callSid)->first();
        if (!$session) return response()->json(['message' => 'Session not found']);

        $user = User::find($session->user_id);
        $astro = User::find($session->astrologer_id);

        $wallet = Wallet::where('user_id', $user->id)->first();
        $astroWallet = Wallet::where('user_id', $astro->id)->first();

        // ✅ CONNECTED
        if ($status === 'answered') {

            $session->update([
                'status' => 'active',
                'started_at' => now()
            ]);

            $astro->update(['is_busy' => true]);
        }

        // ❌ MISSED / BUSY
        if (in_array($status, ['no-answer', 'busy'])) {

            $session->update(['status' => 'missed']);

            $locked = $astro->call_price;

            $wallet->balance += $locked;
            $wallet->locked_balance -= $locked;
            $wallet->locked_balance = max(0, $wallet->locked_balance);
            $wallet->save();

            return response()->json(['message' => 'Call missed']);
        }

        if ($status === 'completed') {

            if ($session->is_deducted) {
                return response()->json(['message' => 'Already processed']);
            }

            if (!$session->started_at) {
                $session->update(['status' => 'missed']);
                return response()->json(['message' => 'Invalid call']);
            }

            $duration = Carbon::parse($session->started_at)->diffInSeconds(now());

            $billableSeconds = ceil($duration / 30) * 30;

            $rate = $astro->call_price / 60;
            $amount = $billableSeconds * $rate;

            $wallet->locked_balance -= $amount;

            $wallet->locked_balance = max(0, $wallet->locked_balance);

            $refund = max(0, $astro->call_price - $amount);
            $wallet->balance += $refund;

            $wallet->total_spent += $amount;
            $wallet->save();

            $earning = $amount * 0.7;

            $astroWallet->balance += $earning;
            $astroWallet->total_earned += $earning;
            $astroWallet->save();

            $session->update([
                'status' => 'completed',
                'ended_at' => now(),
                'duration' => $duration,
                'billable_seconds' => $billableSeconds,
                'amount' => $amount,
                'is_deducted' => true
            ]);

            $astro->update(['is_busy' => false]);
        }

        return response()->json(['ok' => true]);
    }

    public function end(Request $request)
    {
        $session = CallSession::findOrFail($request->session_id);

        $session->update([
            'status' => 'completed',
            'ended_at' => now()
        ]);

        return response()->json(['message' => 'Call ended']);
    }

    public function index()
    {
        $user = auth()->user();

        $calls = CallSession::where('user_id', $user->id)
            ->latest()
            ->get();

        return response()->json($calls);
    }

    public function pulse(Request $request)
    {
        $session = CallSession::findOrFail($request->session_id);

        if ($session->status !== 'active') {
            return response()->json(['message' => 'Call not active']);
        }

        $user = User::find($session->user_id);
        $astro = User::find($session->astrologer_id);

        $wallet = Wallet::where('user_id', $user->id)->first();

        $duration = now()->diffInSeconds($session->started_at);

        $billableSeconds = ceil($duration / 30) * 30;

        $rate = $astro->call_price / 60;
        $amount = $billableSeconds * $rate;

        $remaining = $wallet->balance + $wallet->locked_balance - $amount;

        if ($remaining <= ($astro->call_price * 0.5)) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Low balance, please recharge'
            ]);
        }

        if ($remaining <= 0) {

            Http::withHeaders([
                'Authorization' => 'Bearer ' . (new SmartfloService())->getToken(),
            ])->post('https://api-smartflo.tatateleservices.com/v1/call/hangup', [
                'call_id' => $session->call_sid
            ]);

            return response()->json([
                'status' => 'ended',
                'message' => 'Call ended due to low balance'
            ]);
        }

        return response()->json([
            'status' => 'active',
            'duration' => $duration,
            'billable_seconds' => $billableSeconds
        ]);
    }

    public function saveSmartfloToken(Request $request)
    {
        $request->validate([
            'token' => 'required'
        ]);

        \App\Models\SmartfloToken::create([
            'access_token' => $request->token,
            'expires_at' => now()->addDays(365)
        ]);

        return response()->json([
            'message' => 'Token saved successfully'
        ]);
    }
}