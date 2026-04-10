<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'date_ajout',
    ];

    protected $casts = [
        'date_ajout' => 'date',
    ];

    public static function getFavoritesByUser(int $userId) {
        return static::where('user_id', $userId)
                     ->with('property.city', 'property.media')
                     ->get();
    }

    public static function clearAll(int $userId): void {
        static::where('user_id', $userId)->delete();
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
 
    public function property() {
        return $this->belongsTo(Property::class);
    }
}
