<?php

namespace App\Http\Controllers;

use App\Models\Places;
use Illuminate\Http\Request;

class PlacesController extends Controller
{
    //
    public function index()
    {
        $places = Places::all(); // Get all place records from the db

        return view('places', compact('places')); // Return ui with data
    }
}
