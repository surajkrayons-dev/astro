<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\ApiToken;
use Carbon\Carbon;

class EasyGoApiController extends Controller
{
    public function initiateCall(Request $request)
    {
        DB::beginTransaction();
    
        try {
    
            \Log::info('STEP 1: API HIT');
    
            $request->validate([
                'astrologer_id' => 'required|exists:users,id'
            ]);
    
            $user = auth()->user();
    
            if (!$user) {
                throw new \Exception("User not authenticated");
            }
    
            \Log::info('STEP 2: USER', ['id' => $user->id]);
    
            $wallet = DB::table('wallets')->where('user_id', $user->id)->first();
    
            if (!$wallet || $wallet->balance < 10) {
                throw new \Exception("Low balance");
            }
    
            $astro = DB::table('users')
                ->where('id', $request->astrologer_id)
                ->where('type', 'astro')
                ->first();
    
            if (!$astro || $astro->is_online == 0 || $astro->is_busy == 1) {
                throw new \Exception("Astrologer not available");
            }
    
            \Log::info('STEP 3: ASTRO OK', ['astro_id' => $astro->id]);
    
            $customerNumber = $this->formatNumber($user->mobile);
            $agentNumber    = $this->formatNumber($astro->mobile);
    
            \Log::info('STEP 4: NUMBERS', [
                'customer' => $customerNumber,
                'agent' => $agentNumber
            ]);
    
            // 🔥 TOKEN
            $token = $this->getToken();
    
            if (!$token) {
                throw new \Exception("Token NULL");
            }
    
            // ✅ CONSOLE + LOG TOKEN
            echo "<script>console.log('TOKEN:', '$token');</script>";
    
            \Log::info('🔥 TOKEN GENERATED', ['token' => $token]);
    
            // 🔥 DIAL
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'API_TOKEN'    => $token
            ])->post('https://client.easygoivr.com/easygoapiJwt/request/dial', [
                "number" => $customerNumber,
                "exten"  => $agentNumber,
                "did"    => env('SMARTFLO_CALLER_ID')
            ]);
    
            $result = $response->json();
    
            \Log::info('STEP 6: DIAL RESPONSE', $result);
    
            if (!$response->ok() || ($result['status'] ?? '') !== 'Success') {
                throw new \Exception("Dial failed: " . json_encode($result));
            }
    
            // ✅ SAVE SESSION (UPDATED COLUMN NAMES)
            $sessionId = DB::table('call_sessions')->insertGetId([
                'user_id'        => $user->id,
                'astrologer_id'  => $astro->id,
                'user_number'    => $customerNumber,
                'astro_number'   => $agentNumber,
                'started_at'     => now(),
                'status'         => 'active',
                'created_at'     => now(),
                'updated_at'     => now()
            ]);
    
            // ✅ ASTRO BUSY
            DB::table('users')->where('id', $astro->id)->update([
                'is_busy' => 1
            ]);
    
            DB::commit();
    
            return response()->json([
                'status' => true,
                'message' => 'Calling...',
                'session_id' => $sessionId,
                'token' => $token // 👈 POSTMAN me bhi dikhega
            ]);
    
        } catch (\Exception $e) {
    
            DB::rollBack();
    
            \Log::error('❌ FINAL ERROR', [
                'msg' => $e->getMessage()
            ]);
    
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function callWebhook(Request $request)
    {
        \Log::info('📞 WEBHOOK HIT', $request->all());
    
        // 🔥 NUMBERS (smart detect)
        $userNumber = $this->formatNumber(
            $request->input('Caller') 
            ?? $request->input('caller_id')
        );
    
        $astroNumber = $this->formatNumber(
            $request->input('Agent') 
            ?? $request->input('extension_no')
        );
    
        // 🔥 STATUS FIX
        $status = strtolower(
            $request->input('CallStatus') 
            ?? $request->input('action') 
            ?? ''
        );
    
        // 🔥 SESSION FIND (flexible)
        $session = DB::table('call_sessions')
            ->where(function ($q) use ($userNumber, $astroNumber) {
                $q->where('user_number', $userNumber)
                  ->orWhere('astro_number', $userNumber);
            })
            ->whereNull('ended_at')
            ->latest()
            ->first();
    
        if (!$session) {
            \Log::error('❌ SESSION NOT FOUND', [
                'user' => $userNumber,
                'astro' => $astroNumber
            ]);
            return response()->json(['status' => false]);
        }
    
        // ✅ ANSWER
        if (in_array($status, ['answer', 'answered'])) {
            DB::table('call_sessions')->where('id', $session->id)->update([
                'status' => 'active',
                'started_at' => now(),
                'updated_at' => now()
            ]);
        }
    
        // ✅ HANGUP / COMPLETE
        if (in_array($status, ['hangup', 'completed'])) {
    
            $duration = $request->input('duration')
                ?? $request->input('billsec')
                ?? $request->input('durn')
                ?? 0;
    
            DB::table('call_sessions')->where('id', $session->id)->update([
                'ended_at' => now(),
                'duration' => $duration,
                'status'   => 'completed',
                'updated_at' => now()
            ]);
    
            // 🔥 FREE ASTRO
            DB::table('users')->where('id', $session->astrologer_id)->update([
                'is_busy' => 0
            ]);
        }
    
        return response()->json(['status' => true]);
    }

    private function getToken()
    {
        \Log::info('TOKEN STEP START');

        $res = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->withBasicAuth(
            env('SMARTFLO_USERNAME'),
            env('SMARTFLO_PASSWORD')
        )->post('https://client.easygoivr.com/masterapiJwt/gentoken');

        $data = $res->json();

        \Log::info('TOKEN RESPONSE FULL', [
            'status' => $res->status(),
            'body' => $data
        ]);

        if (!$res->ok() || !isset($data['API_TOKEN'])) {
            throw new \Exception("Token generation failed: " . json_encode($data));
        }

        return $data['API_TOKEN'];
    }

    private function formatNumber($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (substr($phone, 0, 2) === '91') {
            $phone = substr($phone, 2);
        }

        if (substr($phone, 0, 1) !== '0') {
            $phone = '0' . $phone;
        }

        return $phone;
    }
}