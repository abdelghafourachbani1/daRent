<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    use HasFactory;

    protected $fillable = ['nom_ville', 'region'];

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}