<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'tenant_id',
        'owner_id',
        'date_debut',
        'date_fin',
        'prix_total',
        'status',
        'message',
    ];
 
    protected $casts = [
        'date_debut' => 'date',
        'date_fin'   => 'date',
        'prix_total' => 'decimal:2',
    ];

    public static function createReservation(array $data): self {
        $property  = Property::findOrFail($data['property_id']);
        $dateDebut = \Carbon\Carbon::parse($data['date_debut']);
        $dateFin   = \Carbon\Carbon::parse($data['date_fin']);
        $days      = $dateDebut->diffInDays($dateFin);
 
        $total = ($property->prix_mensuel / 30) * $days;
 
        return static::create([
            'property_id' => $data['property_id'],
            'tenant_id'   => $data['tenant_id'],
            'owner_id'    => $property->user_id,
            'date_debut'  => $data['date_debut'],
            'date_fin'    => $data['date_fin'],
            'prix_total'  => round($total, 2),
            'message'     => $data['message'] ?? null,
            'status'      => 'pending',
        ]);
    }

    public function calculateTotal(): float {
        $days = $this->date_debut->diffInDays($this->date_fin);
        return round(($this->property->prix_mensuel / 30) * $days, 2);
    }

    public function accept(): void {
        $this->update(['status' => 'accepted']);
 
        $this->property->update([
            'status' => 'rented',
            'availability' => false,
        ]);
    }

    public function refused(): void {
        $this->update(['status' => 'rejected']);
    }

    public function cancel(): void {
        $this->update(['status' => 'rejected']);
 
        if ($this->property->status === 'rented') {
            $this->property->update([
                'status'       => 'available',
                'availability' => true,
            ]);
        }
    }

    public function isActive(): bool {
        return $this->status === 'accepted'
            && $this->date_debut->isPast()
            && $this->date_fin->isFuture();
    }

    public function checkAvailability(): bool {
        $conflict = static::where('property_id', $this->property_id)
            ->where('status', 'accepted')
            ->where('id', '!=', $this->id)
            ->where(function ($q) {
                $q->where('date_debut', '<', $this->date_fin)
                  ->where('date_fin', '>', $this->date_debut);
            })
            ->exists();
 
        return !$conflict;
    }
 
    public function property() {
        return $this->belongsTo(Property::class);
    }
 
    public function tenant() {
        return $this->belongsTo(User::class, 'tenant_id');
    }
 
    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
