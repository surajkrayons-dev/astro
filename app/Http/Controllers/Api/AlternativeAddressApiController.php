<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\users;
use App\Models\AlternativeAddress;
use Illuminate\Http\Request;

class AlternativeAddressApiController extends Controller
{
    public function index(Request $request)
    {
        $addresses = AlternativeAddress::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $addresses
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'               => 'required|string|max:255',
            'email'             => 'nullable|email|max:255',
            'country_code'       => 'required|string|max:5',
            'mobile'             => 'required|string|max:20',
            'alternative_mobile' => 'nullable|string|max:20',
            'city'               => 'required|string|max:255',
            'state'              => 'required|string|max:255',
            'country'            => 'required|string|max:255',
            'address'            => 'required|string',
            'pincode'            => 'required|string|max:10',
        ]);

        $address = AlternativeAddress::create([
            'user_id'            => $request->user()->id,
            'email'              => $request->email,
            'name'               => $request->name,
            'country_code'       => $request->country_code ?? '+91',
            'mobile'             => $request->mobile,
            'alternative_mobile' => $request->alternative_mobile,
            'city'               => $request->city,
            'state'              => $request->state,
            'country'            => $request->country,
            'address'            => $request->address,
            'pincode'            => $request->pincode,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Address added successfully',
            'data' => $address
        ]);
    }

    public function show(Request $request, $id)
    {
        $address = AlternativeAddress::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return response()->json([
            'status' => true,
            'data' => $address
        ]);
    }

    public function update(Request $request, $id)
    {
        $address = AlternativeAddress::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $request->validate([
            'name'               => 'required|string|max:255',
            'email'             => 'nullable|email|max:255',
            'country_code'       => 'required|string|max:5',
            'mobile'             => 'required|string|max:20',
            'alternative_mobile' => 'nullable|string|max:20',
            'city'               => 'required|string|max:255',
            'state'              => 'required|string|max:255',
            'country'            => 'required|string|max:255',
            'address'            => 'required|string',
            'pincode'            => 'required|string|max:10',
        ]);

        $address->update([
            'name'               => $request->name,
            'email'             => $request->email,
            'country_code'       => $request->country_code ?? '+91',
            'mobile'             => $request->mobile,
            'alternative_mobile' => $request->alternative_mobile,
            'city'               => $request->city,
            'state'              => $request->state,
            'country'            => $request->country,
            'address'            => $request->address,
            'pincode'            => $request->pincode,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Address updated successfully',
            'data' => $address
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $address = AlternativeAddress::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $address->delete();

        return response()->json([
            'status' => true,
            'message' => 'Address deleted successfully'
        ]);
    }
}