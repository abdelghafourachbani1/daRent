<?php

namespace App\Http\Controllers\Api;

use App\Events\ReservationStatusChanged;
use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReservationController extends Controller
{

    public function index(Request $request): JsonResponse {
        $user  = $request->user();
        $query = Reservation::with([
            'property:id,titre,adress,prix_mensuel',
            'tenant:id,nom',
            'owner:id,nom',
        ]);

        if ($user->isTenant()) {
            $query->where('tenant_id', $user->id);
        } else {
            $query->where('owner_id', $user->id);
        }

        return response()->json($query->latest()->paginate(10));
    }

    public function store(Request $request): JsonResponse {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'date_debut'  => 'required|date|after:today',
            'date_fin'    => 'required|date|after:date_debut',
            'message'     => 'nullable|string|max:500',
        ]);

        $property = Property::findOrFail($request->property_id);

        if (!$property->availability) {
            return response()->json([
                'message' => 'Cette propriété n\'est pas disponible.',
            ], 422);
        }

        if ($property->user_id === $request->user()->id) {
            return response()->json([
                'message' => 'Vous ne pouvez pas réserver votre propre propriété.',
            ], 422);
        }

        $existingPending = Reservation::where('property_id', $request->property_id)
            ->where('tenant_id', $request->user()->id)
            ->where('status', 'pending')
            ->exists();

        if ($existingPending) {
            return response()->json([
                'message' => 'Vous avez déjà une demande en attente pour cette propriété.',
            ], 409);
        }

        $reservation = Reservation::createReservation([
            'property_id' => $request->property_id,
            'tenant_id'   => $request->user()->id,
            'date_debut'  => $request->date_debut,
            'date_fin'    => $request->date_fin,
            'message'     => $request->message,
        ]);

        if (!$reservation->checkAvailability()) {
            $reservation->delete();
            return response()->json([
                'message' => 'Les dates sélectionnées ne sont pas disponibles.',
            ], 422);
        }

        $reservation->load(['property:id,titre', 'owner:id,nom']);

        return response()->json([
            'message'     => 'Demande envoyée avec succès',
            'reservation' => $reservation,
        ], 201);
    }

    public function show(Request $request, Reservation $reservation): JsonResponse {
        $user = $request->user();

        if ($reservation->tenant_id !== $user->id && $reservation->owner_id !== $user->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $reservation->load(['property', 'tenant:id,nom,avatar', 'owner:id,nom,avatar']);

        return response()->json([
            'reservation' => $reservation,
            'is_active'   => $reservation->isActive(),
            'total'       => $reservation->calculateTotal(),
        ]);
    }

    public function accept(Request $request, Reservation $reservation): JsonResponse {
        if ($reservation->owner_id !== $request->user()->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        if ($reservation->status !== 'pending') {
            return response()->json([
                'message' => 'Cette demande a déjà été traitée.',
            ], 422);
        }

        $reservation->accept();

        broadcast(new ReservationStatusChanged($reservation->fresh()));

        return response()->json([
            'message'     => 'Demande acceptée',
            'reservation' => $reservation->fresh(),
        ]);
    }

    public function reject(Request $request, Reservation $reservation): JsonResponse
    {
        if ($reservation->owner_id !== $request->user()->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        if ($reservation->status !== 'pending') {
            return response()->json(['message' => 'Cette demande a déjà été traitée.'], 422);
        }

        $reservation->refused();

        broadcast(new ReservationStatusChanged($reservation->fresh()));

        return response()->json([
            'message'     => 'Demande rejetée',
            'reservation' => $reservation->fresh(),
        ]);
    }

    public function cancel(Request $request, Reservation $reservation): JsonResponse
    {
        if ($reservation->tenant_id !== $request->user()->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        if ($reservation->status === 'rejected') {
            return response()->json(['message' => 'Cette demande est déjà annulée.'], 422);
        }

        $reservation->cancel();

        return response()->json([
            'message'     => 'Demande annulée',
            'reservation' => $reservation->fresh(),
        ]);
    }
}