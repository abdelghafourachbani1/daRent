<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Review;
use Illuminate\Http\Request;
use JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReviewController extends Controller
{
    public function store(Request $request, int $propertyId): JsonResponse {
        $property = Property::findOrFail($propertyId);

        $request->validate([
            'note' => 'required|integer|min:1,|max:5',
            'commentaire' => 'required|string|max:1000',
        ]);

        $review = Review::updateOrCreate(
            ['user_id' => $request->user()->id, 'property_id' => $propertyId],
            [
                'note' => $request->note,
                'commentaire' => $request->commentaire,
                'date_publication' => now()->toDateString(),
            ]
        );
        return response()->json([
            'message' => 'review publie successfuly',
            'review' => $review,
        ],201);
    }

    public function index(int $propertyId): JsonResponse
    {
        $reviews = Review::where('property_id', $propertyId)
                         ->with('user:id,nom,avatar')
                         ->latest()
                         ->get();
 
        return response()->json(['reviews' => $reviews]);
    }
}
