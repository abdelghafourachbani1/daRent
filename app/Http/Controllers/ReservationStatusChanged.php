<?php

namespace App\Events;

use Illuminate\Http\Request;
use App\Models\Reservation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReservationStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
 
    public Reservation $reservation;
 
    public function __construct(Reservation $reservation) {
        $this->reservation = $reservation->load([
            'property:id,titre,adress',
            'owner:id,nom',
        ]);
    }

    public function broadcastOn(): array {
        return [
            new PrivateChannel('reservations.' . $this->reservation->tenant_id),
        ];
    }
 
    public function broadcastAs(): string {
        return 'ReservationStatusChanged';
    }
 
    public function broadcastWith(): array {
        return [
            'reservation' => [
                'id'         => $this->reservation->id,
                'status'     => $this->reservation->status,
                'date_debut' => $this->reservation->date_debut,
                'date_fin'   => $this->reservation->date_fin,
                'prix_total' => $this->reservation->prix_total,
                'property'   => $this->reservation->property,
                'owner'      => $this->reservation->owner,
            ],
        ];
    }
}
