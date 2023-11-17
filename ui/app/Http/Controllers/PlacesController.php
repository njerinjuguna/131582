<?php

namespace App\Http\Controllers;

use App\Models\Places;
use Illuminate\Http\Request;

class PlacesController extends Controller
{
    //
    public function index()
    {
        $places = Places::all();

        return view('places', compact('places'));
    }
}
