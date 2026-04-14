<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'city_id',
        'category_id',
        'titre',
        'description',
        'adress',
        'prix_mensuel',
        'type',
        'status',
        'bedrooms', 
        'bathrooms',
        'availability',
        'date_envoie',
    ];

    protected $casts = [
        'availability' => 'boolean',
        'prix_mensuel' => 'decimal:2',
        'date_envoie'  => 'date',
    ];

    public function publish(): bool {
        return $this->update([
            'status' => 'available',
            'availability' => true,
            'date_envoie' => now()->toDateString(),
        ]);
    }

    public function archive(): void {
        $this->update([
            'status' => 'archived',
            'availability' => false,
        ]);
    } 

    public function updateDetails(array $data): void {
        $this->update($data);
    }

    public function calculateAverageRating(): float {
        return round($this->reviews()->avg('note') ?? 0, 1);
    }

    public function owner() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function city() {
        return $this->belongsTo(City::class);
    }

    public function category() {
        return $this->belongsTo(category::class);
    }

    public function equipements() {
        return $this->belongsToMany(Equipement::class, 'property_equipement');
    }

}
