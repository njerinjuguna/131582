<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;

class ScraperController extends Controller
{
    //
    public function index() {

        $client = new Client();
        $website = $client->request('GET', 'https://www.airbnb.com/');

        // dd($client);
        
        dd($website->html());

    }
}
