<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiAstrologer;

class AiAstrologyController extends Controller
{
    public function index()
    {
        $astrologers = AiAstrologer::with([
            'expertises:id,ai_astrologer_id,name,slug'
        ])
        ->where('status', true)
        ->select(
            'id',
            'name',
            'slug',
            'image',
            'chat_price',
            'experience',
            'education',
            'about'
        )
        ->get()
        ->map(function ($astrologer) {

            $astrologer->image = asset('storage/aiAstrologers/' . basename($astrologer->image));

            return $astrologer;
        });

        return response()->json([
            'status' => true,
            'data' => $astrologers
        ]);
    }

    public function show($slug)
    {
        $astrologer = AiAstrologer::with([
            'expertises:id,ai_astrologer_id,name,slug'
        ])
        ->where('slug', $slug)
        ->where('status', true)
        ->firstOrFail([
            'id',
            'name',
            'slug',
            'image',
            'chat_price',
            'experience',
            'education',
            'about'
        ]);

        $astrologer->image = asset('storage/aiAstrologers/' . basename($astrologer->image));

        return response()->json([
            'status' => true,
            'data' => $astrologer
        ]);
    }
}