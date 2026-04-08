<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Equipement extends Model
{
    use HasFactory;

    protected $fillable = ['nom_equipement', 'icon'];

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'property_equipement');
    }
}