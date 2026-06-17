<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiChatSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiChatSessionApiController extends Controller
{
    public function index(): JsonResponse
    {
        $sessions = AiChatSession::with([
                'user:id,name,email',
                'topic'
            ])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sessions,
        ]);
    }

    public function show($id): JsonResponse
    {
        $session = AiChatSession::with([
                'user:id,name,email',
                'topic'
            ])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $session,
        ]);
    }
}