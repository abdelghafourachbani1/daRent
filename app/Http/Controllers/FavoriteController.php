<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use function Symfony\Component\Clock\now;

class FavoriteController extends Controller
{

    public function index(Request $request): JsonResponse {
        $favorites = Favorite::getFavoritesByUser($request->user()->id);

        return response()->json([
            'favorites' => $favorites,
            'total' => $favorites->count(),
        ]);
    }

    public function store(Request $request, int $propertyId): JsonResponse {
        $property = Property::findOrFail($propertyId);
        [$favorite, $created] = [
            Favorite::firstOrCreate(
                ['user_id' => $request->user()->id, 'property_id' => $propertyId],
                ['date_ajout' => now()->toDateString()]
            ),
            false
        ];

        $wasNew = !Favorite::where('user_id', $request->user()->id)
                           ->where('property_id', $propertyId)
                           ->whereDate('date_ajout', today())
                           ->exists();
 
        if (!$wasNew) {
            return response()->json([
                'message' => 'Cette propriété est déjà dans vos favoris.',
            ], 409); 
        }
 
        Favorite::firstOrCreate(
            ['user_id' => $request->user()->id, 'property_id' => $propertyId],
            ['date_ajout' => now()->toDateString()]
        );
 
        return response()->json([
            'message'  => 'Propriété ajoutée aux favoris',
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
