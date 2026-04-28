<?php

namespace App\Http\Controllers;
 
use App\Models\Property;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PropertyController extends Controller {

    public function index(): JsonResponse {

        $properties = Property::with(['owner','city','category','media'])
            ->where('status','available')
            ->where('availability',true)
            ->latest()
            ->paginate(12);

        return response()->json($properties);
    }

    public function show(Property $property): JsonResponse {
        $property->load([
            'owner',
            'city',
            'category',
            'media',
            'equipements',
            'reviews',
        ]);
        $averageRating = $property->calculateAverageRating();

        return response()->json([
            'property' => $property,
            'average_rating' => $averageRating,
        ]);
    }

    public function store(Request $request): JsonResponse {
        $request->validate([
            'titre'       => 'required|string|max:255',
            'description' => 'required|string',
            'adress'      => 'required|string|max:500',
            'prix_mensuel'=> 'required|numeric|min:0',
            'type'        => 'required|string',
            'city_id'     => 'nullable|exists:cities,id',
            'category_id' => 'nullable|exists:categories,id',
            'bedrooms'    => 'nullable|integer|min:0',
            'bathrooms'   => 'nullable|integer|min:0',
            'equipements' => 'nullable|array',
            'equipements.*'=> 'exists:equipements,id',
        ]);

        $property = Property::create([
            'user_id'     => $request->user()->id,
            'titre'       => $request->titre,
            'description' => $request->description,
            'adress'      => $request->adress,
            'prix_mensuel'=> $request->prix_mensuel,
            'type'        => $request->type,
            'city_id'     => $request->city_id,
            'category_id' => $request->category_id,
            'bedrooms'    => $request->bedrooms ?? 1,
            'bathrooms'   => $request->bathrooms ?? 1,
            'status'      => 'available',
            'availability'=> true,
            'date_envoie' => now()->toDateString(),
        ]);

        if ($request->filled('equipements')) {
            $property->equipements()->sync($request->equipements);
        }
        $property->load(['city','category','equipements']);

        return response()->json([
            'message' => 'property cree avec succes',
            'property' => $property,
        ],201);
    }

    public function update(Request $request, Property $property): JsonResponse {
        if ($request->user()->id !== $property->user_id) {
            return response()->json([
                'message' => 'Vous ne pouvez modifier que vos propres propriétés',
            ],403);
        }

        $request->validate([
            'titre'       => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'adress'      => 'sometimes|string|max:500',
            'prix_mensuel'=> 'sometimes|numeric|min:0',
            'type'        => 'sometimes|string',
            'city_id'     => 'sometimes|nullable|exists:cities,id',
            'category_id' => 'sometimes|nullable|exists:categories,id',
            'bedrooms'    => 'sometimes|integer|min:0',
            'bathrooms'   => 'sometimes|integer|min:0',
            'availability'=> 'sometimes|boolean',
            'equipements' => 'sometimes|array',
            'equipements.*'=> 'exists:equipements,id',
        ]);

        $property->updateDetails($request->only([
            'titre', 'description','adress', 'prix_mensuel',
            'type', 'city_id', 'category_id', 'bedroms', 'nathroms', 'availability',
        ]));

        if ($request->has('equipements')) {
            $property->equipements()->sync($request->equipements);
        }

        return response()->json([
            'message' => 'property updated successfuly',
            'property' => $property->fresh()->load(['city','category','equipements']),
        ]);
    }

    public function destroy(Request $request, Property $property): JsonResponse {
        if ($request->user()->id !== $property->user_id) {
            return response()->json([
                'message' => 'Vous ne pouvez supprimer que vos propres property',
            ], 403);
        }
 
        $property->delete();
 
        return response()->json([
            'message' => 'property suprime avec succes',
        ]);
    }

    public function myProperties(Request $request): JsonResponse {
        $properties = Property::with(['city','category','media'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(12);
        return response()->json($properties);
    }

    public function markAsRented(Request $request, Property $property): JsonResponse {
        if ($request->user()->id !== $property->user_id) {
            return response()->json(['message' => 'acces refuser'],403);
        }

        $property->update([
            'status' => 'rented',
            'availability' => false,
        ]);

        return response()->json([
            'message' => 'propertie marque comme lie',
            'property' => $property->fresh(),
        ]);
    }

    public function uploadImages(Request $request, Property $property): JsonResponse {
        if ($request->user()->id !== $property->user_id) {
            return response()->json(['message' => 'acces refuse'], 403);
        }

        $request->validate([
            'images' => 'required|array|max:5',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $uploaded = [];

        foreach ($request->file('images') as $file) {
            $path = $file->store('properties', 'public');
            $media = Media::create([
                'property_id' => $property->id,
                'url_fichier' => $path, 
                'type_fichier' => 'image',
            ]);

            $uploaded[] = $media;
        }

        return response()->json([
            'message' => count($uploaded) . ' images saved successfully',
            'media' => $uploaded,
        ], 201);
    }

    public function stats(Request $request, Property $property): JsonResponse {
        if ($request->user()->id !== $property->user_id) {
            return response()->json([
                'message' => 'acces refuse',
            ],403);
        }

        return response()->json([
            'property_id'   => $property->id,
            'titre'         => $property->titre,
            'nombre_favoris' => $property->favorites()->count(),
            'nombre_messages'=> $property->messages()->count(),
            'nombre_vues'    => 0, 
            'note_moyenne'   => $property->calculateAverageRating(),
        ]);
    }
}