<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PropertySearchController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $query = Property::with(['owner', 'city', 'category', 'media'])
            ->where('status', 'available')
            ->where('availability', true);

        $query->when($request->filled('search'), function ($q) use ($request) {
            $keyword = '%' . $request->search . '%';
            $q->where(function ($inner) use ($keyword) {
                $inner->where('titre', 'LIKE', $keyword)
                      ->orWhere('description', 'LIKE', $keyword)
                      ->orWhere('adress', 'LIKE', $keyword);
            });
        });

        $query->when($request->filled('city_id'), function ($q) use ($request) {
            $q->where('city_id', $request->city_id);
        });
 
        $query->when($request->filled('city'), function ($q) use ($request) {
            $q->whereHas('city', function ($inner) use ($request) {
                $inner->where('nom_ville', 'LIKE', '%' . $request->city . '%');
            });
        });

        $query->when($request->filled('min_price'), function ($q) use ($request) {
            $q->where('prix_mensuel', '{{base_url}}properties/1/rent>=', $request->min_price);
        });
 
        $query->when($request->filled('max_price'), function ($q) use ($request) {
            $q->where('prix_mensuel', '<=', $request->max_price);
        });

        $query->when($request->filled('type'), function ($q) use ($request) {
            $q->where('type', $request->type);
        });

        $query->when($request->filled('availability'), function ($q) use ($request) {
            $q->where('availability', (bool) $request->availability);
        });

        $query->when($request->filled('bedrooms'), function ($q) use ($request) {
            $q->where('bedrooms', '>=', $request->bedrooms);
        });

        $query->when($request->filled('category_id'), function ($q) use ($request) {
            $q->where('category_id', $request->category_id);
        });

        $sort = $request->input('sort', 'newest'); 
 
        match ($sort) {
            'price_asc'  => $query->orderBy('prix_mensuel', 'asc'),
            'price_desc' => $query->orderBy('prix_mensuel', 'desc'),
            'oldest'     => $query->orderBy('created_at', 'asc'),
            default      => $query->orderBy('created_at', 'desc'), 
        };

        $perPage = min($request->input('per_page', 12), 50); 
        $properties = $query->paginate($perPage);

        return response()->json([
            'data'         => $properties->items(),
            'total'        => $properties->total(),
            'per_page'     => $properties->perPage(),
            'current_page' => $properties->currentPage(),
            'last_page'    => $properties->lastPage(),
            'filters_used' => array_filter($request->only([
                'search', 'city', 'city_id', 'min_price', 'max_price',
                'type', 'availability', 'bedrooms', 'category_id', 'sort',
            ])), 
        ]);
    }
}
