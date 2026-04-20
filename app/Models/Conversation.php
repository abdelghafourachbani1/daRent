<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'tenant_id',
        'owner_id',
        'property_id',
    ];

    public function isParticipant(int $userId): bool {
        // to join a channel he shouls be (tenant or owner)
        return $this->tenant_id === $userId || $this->owner_id === $userId;
    }
 
    public function tenant() {
        return $this->belongsTo(User::class, 'tenant_id');
    }
 
    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }
 
    public function property() {
        return $this->belongsTo(Property::class);
    }
 
    public function messages() {
        return $this->hasMany(Message::class);
    }
 
    public function latestMessage() {
        return $this->hasOne(Message::class)->latestOfMany();
    }
}
