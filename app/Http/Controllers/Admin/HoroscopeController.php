<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Models\Horoscope;
use App\Models\ZodiacSign;
use Illuminate\Http\Request;

class HoroscopeController extends AdminController
{
    public function getIndex(Request $request)
    {
        return view('admin.horoscopes.index');
    }

    public function getList(Request $request)
    {
        $list = Horoscope::select('horoscopes.*')
            ->with('zodiac')
            ->when($request->zodiac_id, function ($q) use ($request) {
                $q->where('zodiac_id', $request->zodiac_id);
            })
            ->when($request->type, function ($q) use ($request) {
                $q->where('type', $request->type);
            })
            ->when($request->status !== null && $request->status !== '', function ($q) use ($request) {
                $q->where('horoscopes.status', $request->status);
            })
            ->latest();

        return \DataTables::of($list)
            ->addColumn('zodiac_name', fn ($row) => $row->zodiac->name ?? '')
            ->addColumn('status_text', fn ($row) => $row->status == 1 ? 'Active' : 'Inactive')
            ->rawColumns(['zodiac_name'])
            ->make();
    }

    public function getCreate(Request $request)
    {
        $zodiacs = ZodiacSign::where('status', 1)->get();

        return view('admin.horoscopes.create', compact('zodiacs'));
    }

    public function postCreate(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'zodiac_id' => 'required|exists:zodiac_signs,id',
            'type' => 'required|in:today,yesterday,tomorrow,daily,weekly,monthly,yearly',

            'title' => 'required|string',
            'overview' => 'nullable|string',

            'love' => 'nullable|string',
            'career' => 'nullable|string',
            'health' => 'nullable|string',
            'finance' => 'nullable|string',
            'family' => 'nullable|string',
            'students' => 'nullable|string',
            'warning' => 'nullable|string',

            'career_date' => 'nullable|array',
            'career_date.*' => 'integer',

            'finance_date' => 'nullable|array',
            'finance_date.*' => 'integer',

            'love_date' => 'nullable|array',
            'love_date.*' => 'integer',

            'health_date' => 'nullable|array',
            'health_date.*' => 'integer',

            'family_date' => 'nullable|array',
            'family_date.*' => 'integer',

            'students_date' => 'nullable|array',
            'students_date.*' => 'integer',

            'lucky_numbers' => 'nullable|array',
            'lucky_numbers.*' => 'integer',

            'lucky_colors' => 'nullable|array',
            'lucky_colors.*' => 'string',

            'status' => 'nullable|in:1,0',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        try {
            $horoscope = new \App\Models\Horoscope;

            $horoscope->zodiac_id = $request->zodiac_id;
            $horoscope->type = $request->type;

            $horoscope->title = $request->title;
            $horoscope->overview = $request->overview;

            $horoscope->love = $request->love;
            $horoscope->career = $request->career;
            $horoscope->health = $request->health;
            $horoscope->finance = $request->finance;
            $horoscope->family = $request->family;
            $horoscope->students = $request->students;
            $horoscope->warning = $request->warning;

            $horoscope->career_date = $request->career_date;
            $horoscope->finance_date = $request->finance_date;
            $horoscope->love_date = $request->love_date;
            $horoscope->health_date = $request->health_date;
            $horoscope->family_date = $request->family_date;
            $horoscope->students_date = $request->students_date;

            $horoscope->lucky_numbers = $request->lucky_numbers;
            $horoscope->lucky_colors = $request->lucky_colors;

            $horoscope->status = $request->status ?? 1;
            $horoscope->created_by = auth()->id();

            $horoscope->save();

            return response()->json([
                'message' => 'Horoscope created successfully.',
                'data' => $horoscope
            ]);

        } catch (\Throwable $th) {
            \Log::error($th);

            return response()->json([
                'message' => 'Failed to create horoscope. Please try again later.'
            ], 422);
        }
    }

    public function getUpdate(Request $request)
    {
        $horoscope = Horoscope::findOrFail($request->id);
        $zodiacs = ZodiacSign::where('status', 1)->get();

        return view('admin.horoscopes.update', compact('horoscope', 'zodiacs'));
    }

    public function postUpdate(Request $request, $id)
    {
        $validator = \Validator::make($request->all(), [
            'zodiac_id' => 'required|exists:zodiac_signs,id',
            'type' => 'required|in:today,yesterday,tomorrow,daily,weekly,monthly,yearly',

            'title' => 'required|string',
            'overview' => 'nullable|string',

            'love' => 'nullable|string',
            'career' => 'nullable|string',
            'health' => 'nullable|string',
            'finance' => 'nullable|string',
            'family' => 'nullable|string',
            'students' => 'nullable|string',
            'warning' => 'nullable|string',

            'career_date' => 'nullable|array',
            'career_date.*' => 'integer',

            'finance_date' => 'nullable|array',
            'finance_date.*' => 'integer',

            'love_date' => 'nullable|array',
            'love_date.*' => 'integer',

            'health_date' => 'nullable|array',
            'health_date.*' => 'integer',

            'family_date' => 'nullable|array',
            'family_date.*' => 'integer',

            'students_date' => 'nullable|array',
            'students_date.*' => 'integer',

            'lucky_numbers' => 'nullable|array',
            'lucky_numbers.*' => 'integer',

            'lucky_colors' => 'nullable|array',
            'lucky_colors.*' => 'string',

            'status' => 'nullable|in:1,0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $horoscope = \App\Models\Horoscope::findOrFail($id);

            $horoscope->zodiac_id = $request->zodiac_id;
            $horoscope->type = $request->type;

            $horoscope->title = $request->title;
            $horoscope->overview = $request->overview;

            $horoscope->love = $request->love;
            $horoscope->career = $request->career;
            $horoscope->health = $request->health;
            $horoscope->finance = $request->finance;
            $horoscope->family = $request->family;
            $horoscope->students = $request->students;
            $horoscope->warning = $request->warning;

            $horoscope->career_date = $request->career_date;
            $horoscope->finance_date = $request->finance_date;
            $horoscope->love_date = $request->love_date;
            $horoscope->health_date = $request->health_date;
            $horoscope->family_date = $request->family_date;
            $horoscope->students_date = $request->students_date;

            $horoscope->lucky_numbers = $request->lucky_numbers;
            $horoscope->lucky_colors = $request->lucky_colors;

            $horoscope->status = $request->status ?? 1;
            $horoscope->modified_by = auth()->id();

            $horoscope->save();

            return response()->json([
                'message' => 'Horoscope updated successfully.',
                'data' => $horoscope
            ]);

        } catch (\Throwable $th) {

            \Log::error($th);

            return response()->json([
                'message' => 'Failed to update horoscope. Please try again later.',
            ], 500);
        }
    }

    public function getDelete(Request $request)
    {
        $horoscope = \App\Models\Horoscope::findOrFail($request->id);

        $horoscope->delete();

        return response()->json(['message' => 'Your request processed successfully.']);
    }

    public function getChangeStatus(Request $request)
    {
        $horoscope = \App\Models\Horoscope::findOrFail($request->id);
        if (! blank($horoscope)) {
            $horoscope->status = (int) ! $horoscope->status;
            $horoscope->save();
        }

        return response()->json(['message' => 'Your request processed successfully.']);
    }
}