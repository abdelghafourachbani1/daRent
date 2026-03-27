<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

use function Pest\Laravel\json;

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

    public function update(Request $request , $id) {
        $property = Property::findOrFail($id);

        if ($property->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Forbidden'
            ],403);
        }
        $property->update($request->all());
        return response()->json($property);
    }

}
