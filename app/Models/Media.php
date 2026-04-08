<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Media extends Model
{
    use HasFactory;

    protected $fillable = ['property_id', 'url_fichier', 'type_fichier'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}