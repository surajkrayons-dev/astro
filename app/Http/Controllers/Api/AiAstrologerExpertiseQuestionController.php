<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiAstrologerExpertise;

class AiAstrologerExpertiseQuestionController extends Controller
{
    public function index($slug)
    {
        $expertise = AiAstrologerExpertise::with([
            'questions:id,expertise_id,question'
        ])
        ->where('slug', $slug)
        ->where('status', true)
        ->firstOrFail([
            'id',
            'ai_astrologer_id',
            'name',
            'slug'
        ]);

        return response()->json([
            'status' => true,
            'data' => $expertise
        ]);
    }
}