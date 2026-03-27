<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{

    public function index() {
        return response()->json(Property::with('user')->latest()->get());
    }

    public function store(Request $request) {
        if ($request->user()->role !== 'owner') {
            return response()->json([
                'message' => 'Unauthorized'
            ],403);
        }
        $data = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'city' => 'required',
            'adress' => 'required',
            'type' => 'required'
        ]);

        $property = Property::create([
            ...$data,
            'user_id' => $request->user()->id()
        ]);

        return response()->json($property,201);
    }

}
