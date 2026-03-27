<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{

    public function index() {
        return response()->json(Property::with('user')->latest()->get());
    }

}
