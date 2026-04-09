<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Horoscope;
use Illuminate\Http\Request;

class HoroscopeApiController extends Controller
{
    // All horoscopes
    public function index(Request $request)
    {
        $query = Horoscope::with([
                'zodiac',      
                'creator:id,name,email',
                'modifier:id,name,email'
            ])
            ->where('status', 1);

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->zodiac_id) {
            $query->where('zodiac_id', $request->zodiac_id);
        }

        $data = $query->latest()->get();

        return response()->json([
            'status' => true,
            'count' => $data->count(),
            'data' => $data->map(function ($row) {
                return [
                    'id' => $row->id,
                    'type' => $row->type,
                    'title' => $row->title,
                    'overview' => $row->overview,

                    'zodiac' => [
                        'id' => $row->zodiac->id ?? null,
                        'name' => $row->zodiac->name ?? null,
                        'slug' => $row->zodiac->slug ?? null,
                        'icon' => $row->zodiac->icon ?? null,
                    ],

                    'sections' => [
                        'career' => [
                            'text' => $row->career,
                            'dates' => $row->career_date,
                        ],
                        'finance' => [
                            'text' => $row->finance,
                            'dates' => $row->finance_date,
                        ],
                        'love' => [
                            'text' => $row->love,
                            'dates' => $row->love_date,
                        ],
                        'health' => [
                            'text' => $row->health,
                            'dates' => $row->health_date,
                        ],
                        'family' => [
                            'text' => $row->family,
                            'dates' => $row->family_date,
                        ],
                        'students' => [
                            'text' => $row->students,
                            'dates' => $row->students_date,
                        ],
                    ],

                    'warning' => $row->warning,

                    'lucky' => [
                        'numbers' => $row->lucky_numbers,
                        'colors' => $row->lucky_colors,
                    ],

                    'status' => $row->status,

                    'created_by' => [
                        'id' => $row->creator->id ?? null,
                        'name' => $row->creator->name ?? null,
                        'email' => $row->creator->email ?? null,
                    ],

                    'modified_by' => [
                        'id' => $row->modifier->id ?? null,
                        'name' => $row->modifier->name ?? null,
                        'email' => $row->modifier->email ?? null,
                    ],

                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ];
            }),
        ]);
    }

    // Filter horoscope
    public function show($id)
    {
        $row = Horoscope::with([
                'zodiac',
                'creator:id,name,email',
                'modifier:id,name,email'
            ])
            ->where('status', 1)
            ->find($id);

        if (! $row) {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $row->id,
                'type' => $row->type,
                'title' => $row->title,
                'overview' => $row->overview,

                'zodiac' => $row->zodiac,

                'sections' => [
                    'career' => [
                        'text' => $row->career,
                        'dates' => $row->career_date,
                    ],
                    'finance' => [
                        'text' => $row->finance,
                        'dates' => $row->finance_date,
                    ],
                    'love' => [
                        'text' => $row->love,
                        'dates' => $row->love_date,
                    ],
                    'health' => [
                        'text' => $row->health,
                        'dates' => $row->health_date,
                    ],
                    'family' => [
                        'text' => $row->family,
                        'dates' => $row->family_date,
                    ],
                    'students' => [
                        'text' => $row->students,
                        'dates' => $row->students_date,
                    ],
                ],

                'warning' => $row->warning,

                'lucky' => [
                    'numbers' => $row->lucky_numbers,
                    'colors' => $row->lucky_colors,
                ],

                'created_by' => $row->creator,
                'modified_by' => $row->modifier,

                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]
        ]);
    }
}