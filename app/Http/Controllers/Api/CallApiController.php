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
use Illuminate\Support\Facades\DB;

class CallApiController extends Controller
{
    public function start(Request $request)
    {
        return DB::transaction(function () use ($request) {

            // 🔥 SMARTFLO PAYLOAD
            $caller = $request->call_from_number;
            $receiver = $request->call_to_number;

            if (!$caller || !$receiver) {
                return response()->json(['message' => 'Invalid request'], 400);
            }

            // 🧑 USER FIND
            $user = User::where('mobile', substr($caller, -10))
                ->where('type', 'user')
                ->first();

            if (!$user) {
                abort(404, 'User not found');
            }

            // 🔮 ASTRO FIND
            /* $astro = User::where('mobile', substr($receiver, -10))
                ->where('type', 'astro')
                ->first(); */
                
            // 🔮 ASTRO FIND (TEMP HARDCODE FIX)
            $astro = User::where('id', 3)->where('type', 'astro')->first();

            if (!$astro) {
                abort(404, 'Astrologer not found');
            }

            // 🚫 STATUS CHECK
            if (!$astro->is_online || $astro->is_busy) {
                abort(400, 'Astrologer not available');
            }

            // 💰 WALLET CHECK
            $wallet = Wallet::lockForUpdate()
                ->where('user_id', $user->id)
                ->first();

            if (!$wallet || $wallet->balance < $astro->call_price) {
                abort(400, 'Low balance');
            }

            // 🔒 LOCK 1 MIN
            $lock = $astro->call_price;

            $wallet->balance -= $lock;
            $wallet->locked_balance += $lock;
            $wallet->save();

            // 🔑 TOKEN FROM ENV
            $token = env('SMARTFLO_TOKEN');

            if (!$token) {
                abort(500, 'Smartflo token missing');
            }

            // 📞 CALL API
            $response = Http::withToken($token)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->post(env('SMARTFLO_BASE_URL') . '/v1/clicktocall', [
                    "from" => "916200481457",
                    "to" => "918178268047",
                    "caller_id" => "918069879591",
                    "call_type" => "trans"
                ]);

            if (!$response->successful()) {
                throw new \Exception('Smartflo failed: ' . $response->body());
            }

            $callSid = $response['call_id'] ?? null;

            if (!$callSid) {
                throw new \Exception('Invalid call response');
            }

            // 📊 SESSION
            $session = CallSession::create([
                'user_id' => $user->id,
                'astrologer_id' => $astro->id,
                'call_sid' => $callSid,
                'status' => 'initiated'
            ]);

            return response()->json([
                'session_id' => $session->id,
                'message' => 'Calling...'
            ]);
        });
    }

    public function webhook(Request $request)
    {
        if ($request->header('x-api-key') !== env('SMARTFLO_WEBHOOK_KEY')) {
            abort(403);
        }

        return DB::transaction(function () use ($request) {

            $session = CallSession::where('call_sid', $request->call_id)
                ->lockForUpdate()
                ->firstOrFail();

            $userWallet = Wallet::where('user_id', $session->user_id)->lockForUpdate()->first();
            $astroWallet = Wallet::where('user_id', $session->astrologer_id)->lockForUpdate()->first();

            $astro = User::find($session->astrologer_id);

            // ✅ ANSWERED
            if ($request->status === 'answered') {

                $session->update([
                    'status' => 'active',
                    'started_at' => now()
                ]);

                $astro->update(['is_busy' => true]);
            }

            // ❌ MISSED
            if (in_array($request->status, ['no-answer', 'busy'])) {

                $lock = $astro->call_price;

                $userWallet->balance += $lock;
                $userWallet->locked_balance -= $lock;
                $userWallet->save();

                $session->update(['status' => 'missed']);

                return response()->json(['message' => 'missed']);
            }

            // ✅ COMPLETED
            if ($request->status === 'completed') {

                if ($session->is_deducted) {
                    return response()->json(['ok' => true]);
                }

                if (!$session->started_at) {
                    $session->update(['status' => 'missed']);
                    return response()->json(['invalid']);
                }

                $duration = now()->diffInSeconds($session->started_at);
                $billable = ceil($duration / 60) * 60;

                $rate = $astro->call_price / 60;
                $amount = $billable * $rate;

                // USER
                $userWallet->locked_balance -= $amount;
                $refund = max(0, $astro->call_price - $amount);

                $userWallet->balance += $refund;
                $userWallet->total_spent += $amount;
                $userWallet->save();

                // ASTRO (70%)
                $earning = $amount * 0.7;

                $astroWallet->balance += $earning;
                $astroWallet->total_earned += $earning;
                $astroWallet->save();

                $session->update([
                    'status' => 'completed',
                    'ended_at' => now(),
                    'duration' => $duration,
                    'billable_seconds' => $billable,
                    'amount' => $amount,
                    'is_deducted' => true
                ]);

                $astro->update(['is_busy' => false]);
            }

            return response()->json(['ok' => true]);
        });
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