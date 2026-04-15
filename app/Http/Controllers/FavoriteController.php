<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class FavoriteController extends Controller
{

    public function index(Request $request): JsonResponse {
        $favorites = Favorite::getFavoritesByUser($request->user()->id);

        return response()->json([
            'favorites' => $favorites,
            'total' => $favorites->count(),
        ]);
    }

    public function store(Request $request, int $propertyId): JsonResponse
    {
        $property = Property::findOrFail($propertyId);

        $favorite = Favorite::firstOrCreate(
            ['user_id' => $request->user()->id,'property_id' => $propertyId],
            ['date_ajout' => now()->toDateString()]
        );

        if (!$favorite->wasRecentlyCreated) {
            return response()->json([
                'message' => 'cette property deja exist sur votre favorite',
            ], 409);
        }

        return response()->json([
            'message'  => 'property addedd successfully',
            'property' => $property->only(['id', 'titre', 'prix_mensuel']),
        ], 201);
    }

    public function destroy(Request $request, int $propertyId): JsonResponse {
        $deleted = Favorite::where('user_id', $request->user()->id)
                            ->where('property_id', $propertyId)
                            ->delete();
        if ($deleted) {
            return response()->json([
                'message' => "cette faovrite n'exist pas",
            ],404);
        }

        return response()->json([
            'message' => 'property retirer from favory',
        ]);
    }

    public function clearAll(Request $request): JsonResponse {
        Favorite::clearAll($request->user()->id);
        return response()->json([
            'message' => 'all favorite are deleted',
        ]);
    }

}
