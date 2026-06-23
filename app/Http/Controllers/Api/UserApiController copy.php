<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use App\Models\CallSession;
use App\Models\ChatSession;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class UserApiController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'mobile'   => 'required|digits:10|unique:users,mobile',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6',
            'profile_image' => 'nullable|string',
            'terms_accepted' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = User::create([
            'type' => 'user',
            'role_id' => 3,
            'code' => $this->generateUserCode($request->name),
            'terms_accepted' => $request->terms_accepted,

            'name' => $request->name,
            'email' => strtolower($request->email),
            'mobile' => $request->mobile,
            'username' => strtolower($request->username),
            'password' => bcrypt($request->password),

            'status' => 1,
        ]);
        
        if ($request->filled('profile_image')) {
            $user->profile_image = $this->saveBase64Image(
                $request->profile_image,
                'user'
            );
            $user->save();
        }

        Wallet::create([
            'user_id' => $user->id,
            'balance' => 0,
            'total_added' => 0,
            'total_spent' => 0,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            'token' => $user->createToken('auth_token')->plainTextToken,
            'user' => $user->load('wallet'),
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = User::where('type', 'user')
            ->where('username', $request->username)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid username or password',
            ], 401);
        }

        if (! $user->status) {
            return response()->json([
                'status' => false,
                'message' => 'Account is inactive',
            ], 403);
        }

        $user->update([
            'is_online' => 1,
            'last_seen_at' => now(),
        ]);

        $user->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $user->createToken('auth_token')->plainTextToken,
            'user' => $user,
        ]);
    }

    public function profile(Request $request)
    {
        $user = auth()->user()->load(['wallet']);

        $perPage = (int) $request->get('per_page', 20);
        // call_page, chat_page, recharge_page, reviews_page

        /* ================= TODAY SUMMARY ================= */
        $todayCall = CallSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereDate('started_at', today())
            ->sum('amount');

        $todayChat = ChatSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereDate('started_at', today())
            ->sum('amount');

        $todayRecharge = DB::table('wallet_recharges')
            ->where('wallet_id', optional($user->wallet)->id)
            ->whereDate('recharged_at', today())
            ->sum('amount');

        /* ================= LIFETIME SUMMARY ================= */
        $totalCall = CallSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');

        $totalChat = ChatSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');

        $totalRecharge = DB::table('wallet_recharges')
            ->where('wallet_id', optional($user->wallet)->id)
            ->sum('amount');

        /* ================= CALL HISTORY (PAGINATED) ================= */
        $callPaginator = CallSession::where('user_id', $user->id)
            ->with('astrologer:id,name,code')
            ->orderByDesc('started_at')
            ->paginate($perPage, ['*'], 'call_page');

        $callPaginator->getCollection()->transform(function ($c) {
            return [
                'id' => $c->id,
                'astrologer_name' => $c->astrologer->name ?? null,
                'astrologer_code' => $c->astrologer->code ?? null,
                'started_at' => $c->started_at,
                'ended_at' => $c->ended_at,
                'duration_minutes' => $c->duration,
                'amount' => (float) $c->amount,
                'status' => $c->status,
            ];
        });

        /* ================= CHAT HISTORY (PAGINATED) ================= */
        $chatPaginator = ChatSession::where('user_id', $user->id)
            ->with('astrologer:id,name,code')
            ->orderByDesc('started_at')
            ->paginate($perPage, ['*'], 'chat_page');

        $chatPaginator->getCollection()->transform(function ($c) {
            return [
                'id' => $c->id,
                'astrologer_name' => $c->astrologer->name ?? null,
                'astrologer_code' => $c->astrologer->code ?? null,
                'started_at' => $c->started_at,
                'ended_at' => $c->ended_at,
                'duration_minutes' => $c->duration,
                'amount' => (float) $c->amount,
                'status' => $c->status,
            ];
        });

        /* ================= RECHARGE HISTORY (PAGINATED) ================= */
        $rechargeQuery = DB::table('wallet_recharges')
            ->where('wallet_id', optional($user->wallet)->id)
            ->orderByDesc('recharged_at');

        $rechargePaginator = $rechargeQuery->paginate($perPage, ['*'], 'recharge_page');

        // map recharge items to consistent structure
        $rechargePaginator->getCollection()->transform(function ($r) {
            return [
                'id' => $r->id ?? null,
                'amount' => (float) ($r->amount ?? $r->recharge_amount ?? 0),
                'method' => $r->payment_method ?? null,
                'recharged_at' => $r->recharged_at ?? $r->created_at ?? null,
                'status' => $r->status ?? null,
            ];
        });

        /* ================= REVIEWS GIVEN (PAGINATED) ================= */
        $reviewsQuery = Review::where('user_id', $user->id)
            ->with('astrologer:id,name,code')
            ->orderByDesc('created_at');

        $reviewsPaginator = $reviewsQuery->paginate($perPage, ['*'], 'reviews_page');

        $reviewsPaginator->getCollection()->transform(function ($r) {
            return [
                'id' => $r->id,
                'rating' => (int) $r->rating,
                'review' => $r->review,
                'astrologer_name' => $r->astrologer->name ?? null,
                'astrologer_code' => $r->astrologer->code ?? null,
                'date' => $r->created_at,
            ];
        });

        /* ================= RESPONSE ================= */
        return response()->json([
            'status' => true,

            'user' => [
                'id' => $user->id,
                'code' => $user->code,
                'username' => $user->username,
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'country_code' => $user->country_code,
                'gender' => $user->gender,
                'dob' => $user->dob,
                'birth_time' => $user->birth_time,
                'birth_place' => $user->birth_place,
                'about' => $user->about,
                'address' => $user->address,
                'pincode' => $user->pincode,
                'profile_image' => $user->profile_image ? asset('storage/user/'.$user->profile_image) : null,
            ],

            'wallet' => [
                'balance' => (float) ($user->wallet?->balance ?? 0),
                'total_added' => (float) ($user->wallet?->total_added ?? 0),
                'total_spent' => (float) ($user->wallet?->total_spent ?? 0),
                'last_recharge_amount' => (float) ($user->wallet?->last_recharge_amount ?? 0),
                'last_recharge_at' => $user->wallet?->last_recharge_at,
            ],

            'today_summary' => [
                'call_spent' => (float) $todayCall,
                'chat_spent' => (float) $todayChat,
                'total_spent' => (float) ($todayCall + $todayChat),
                'recharged' => (float) $todayRecharge,
            ],

            'lifetime_summary' => [
                'call_spent' => (float) $totalCall,
                'chat_spent' => (float) $totalChat,
                'total_spent' => (float) ($totalCall + $totalChat),
                'total_recharged' => (float) $totalRecharge,
            ],

            // paginated sections
            'call_history' => [
                'data' => $callPaginator->items(),
                'meta' => [
                    'current_page' => $callPaginator->currentPage(),
                    'last_page' => $callPaginator->lastPage(),
                    'per_page' => $callPaginator->perPage(),
                    'total' => $callPaginator->total(),
                ],
            ],

            'chat_history' => [
                'data' => $chatPaginator->items(),
                'meta' => [
                    'current_page' => $chatPaginator->currentPage(),
                    'last_page' => $chatPaginator->lastPage(),
                    'per_page' => $chatPaginator->perPage(),
                    'total' => $chatPaginator->total(),
                ],
            ],

            'recharge_history' => [
                'data' => $rechargePaginator->items(),
                'meta' => [
                    'current_page' => $rechargePaginator->currentPage(),
                    'last_page' => $rechargePaginator->lastPage(),
                    'per_page' => $rechargePaginator->perPage(),
                    'total' => $rechargePaginator->total(),
                ],
            ],

            'reviews_given' => [
                'data' => $reviewsPaginator->items(),
                'meta' => [
                    'current_page' => $reviewsPaginator->currentPage(),
                    'last_page' => $reviewsPaginator->lastPage(),
                    'per_page' => $reviewsPaginator->perPage(),
                    'total' => $reviewsPaginator->total(),
                ],
            ],
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [

            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'mobile' => 'nullable|digits:10|unique:users,mobile,' . $user->id,
            'country_code' => 'nullable|string|max:5',

            'gender' => 'nullable|in:male,female,other',
            'dob' => 'nullable|date',

            'birth_time' => 'nullable|regex:/^\d{2}:\d{2}(:\d{2})?$/',
            'birth_place' => 'nullable|string|max:255',

            'about' => 'nullable|string|max:2000',
            'address' => 'nullable|string|max:2000',
            'pincode' => 'nullable|string|max:10',

            'astrologer_id' => 'nullable|exists:users,id',
            'rating'        => 'nullable|integer|min:1|max:5',
            'review'        => 'nullable|string|max:2000',

            'profile_image' => 'nullable|string|max:6000000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        \DB::beginTransaction();

        try {

            if ($request->filled('profile_image')) {
                $user->profile_image = $this->saveBase64Image(
                    $request->profile_image,
                    'user',
                    $user->profile_image
                );
            }

            if ($request->has('name'))        $user->name = $request->name;
            if ($request->has('email'))       $user->email = $request->email;
            if ($request->has('mobile'))      $user->mobile = $request->mobile;
            if ($request->has('country_code')) $user->country_code = $request->country_code;

            if ($request->has('gender'))      $user->gender = $request->gender;
            if ($request->has('dob'))         $user->dob = $request->dob;
            if ($request->has('birth_place')) $user->birth_place = $request->birth_place;

            if ($request->has('birth_time')) {
                $time = $request->birth_time;
                if (strlen($time) === 5) { // HH:MM → HH:MM:00
                    $time .= ':00';
                }
                $user->birth_time = $time;
            }

            if ($request->has('about'))   $user->about = $request->about;
            if ($request->has('address')) $user->address = $request->address;
            if ($request->has('pincode')) $user->pincode = $request->pincode;

            $user->modified_by = $user->id;
            $user->save();

            if ($request->filled('astrologer_id') && $request->filled('rating')) {

                $astrologer = User::where('id', $request->astrologer_id)
                    ->where('type', 'astro')
                    ->first();

                if (! $astrologer) {
                    throw new \Exception('Invalid astrologer');
                }

                Review::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'astrologer_id' => $astrologer->id,
                    ],
                    [
                        'rating' => $request->rating,
                        'review' => $request->review,
                    ]
                );

                $stats = Review::where('astrologer_id', $astrologer->id)
                    ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total')
                    ->first();

                $astrologer->update([
                    'rating' => round($stats->avg_rating, 2),
                    'rating_count' => $stats->total,
                ]);
            }

            \DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Profile updated successfully',
                'user' => $user->fresh(),
            ]);

        } catch (\Throwable $e) {

            \DB::rollBack();
            \Log::error($e);

            return response()->json([
                'status' => false,
                'message' => 'Failed to update profile',
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = auth()->user();

        if (! Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Old password does not match',
            ], 401);
        }

        $user->update([
            'password' => bcrypt($request->new_password),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Password changed successfully',
        ]);
    }

    public function logout(Request $request)
    {
        $user = auth()->user();
        $user->update(['is_online' => 0, 'last_seen_at' => now()]);
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logout successful',
        ]);
    }

    public function delete()
    {
        $user = auth()->user();
        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'Account deleted successfully',
        ]);
    }

    private function generateUserCode($name)
    {
        $prefix = strtoupper(substr(trim($name), 0, 3));
        $last = User::where('code', 'like', $prefix.'%')->latest()->first();
        $num = $last && preg_match('/'.$prefix.'(\d+)/', $last->code, $m)
            ? (int)$m[1] + 1
            : 1;

        return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    private function saveBase64Image($base64, $folder = 'user', $oldFile = null)
    {
        $storagePath = storage_path("app/public/{$folder}");
        $publicPath  = public_path("storage/{$folder}");

        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        if (!file_exists($publicPath)) {
            mkdir($publicPath, 0755, true);
        }

        if ($oldFile) {
            if (file_exists($storagePath.'/'.$oldFile)) {
                unlink($storagePath.'/'.$oldFile);
            }
            if (file_exists($publicPath.'/'.$oldFile)) {
                unlink($publicPath.'/'.$oldFile);
            }
        }

        preg_match('/^data:image\/(\w+);base64,/', $base64);
        $data = base64_decode(substr($base64, strpos($base64, ',') + 1));

        $filename = uniqid().'.webp';

        // Save in storage
        Image::make($data)
            ->fit(128,128)
            ->encode('webp',80)
            ->save($storagePath.'/'.$filename);

        // Copy to public
        copy($storagePath.'/'.$filename, $publicPath.'/'.$filename);

        return $filename;
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'type'  => 'required|in:astro,user',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = User::where('email', $request->email)
                    ->where('type', $request->type)
                    ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Account not found for this type',
            ], 404);
        }

        $otp = rand(100000, 999999);

        $user->update([
            'otp' => $otp,
        ]);

        $mailData = [
            'name' => $user->name,
            'otp'  => $otp,
        ];

        \Mail::to($user->email)
            ->send(new \App\Mail\ForgotPasswordMail($mailData));

        return response()->json([
            'status' => true,
            'message' => 'OTP sent to your email',
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'type'  => 'required|in:astro,user',
            'otp'   => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = User::where('email', $request->email)
                    ->where('type', $request->type)
                    ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid account',
            ], 404);
        }

        if ($user->otp != $request->otp) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP',
            ], 400);
        }

        if ($user->updated_at->diffInMinutes(now()) > 10) {
            return response()->json([
                'status' => false,
                'message' => 'OTP expired',
            ], 400);
        }

        return response()->json([
            'status' => true,
            'message' => 'OTP verified successfully',
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'type'  => 'required|in:astro,user',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = User::where('email', $request->email)
                    ->where('type', $request->type)
                    ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid account',
            ], 404);
        }

        $user->update([
            'password' => bcrypt($request->password),
            'otp' => null,
        ]);

        $user->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Password reset successfully',
        ]);
    }
}
