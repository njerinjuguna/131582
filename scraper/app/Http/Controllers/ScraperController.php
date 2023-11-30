<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;
use Goutte\Client;

class ScraperController extends Controller
{
    //
    public function index() {

        $client = new Client();
        $website = $client->request('GET', 'https://www.kayak.com/Nairobi-Hotels.26243.hotel.ksp');

        $placeDivs = $website->filter('.c44F-item');
        $placeNames = [];
        $placePrices = [];
        $placeRatings = [];
        $placeImages = [];
        $placeURLs = [];

        // Filter through the divs and save the scraped data to our places database
        $placeDivs->each(function ($placeDiv) use(&$placeNames, &$placePrices, &$placeRatings, &$placeImages, &$placeURLs) {
            $placeName = $placeDiv->filter('.soom-name')->text();
            $placePrice = $placeDiv->filter('.soom-price')->text();
            $placeRating = $placeDiv->filter('.soom-rating-wrapper')->text();
            $placeImage = $placeDiv->filter('img.EWHU')->attr('src');
            $placeURL = $placeDiv->filter('.soom-photo-wrapper')->attr('href');
            
            $price = preg_replace("/[^0-9]/", "", $placePrice);
            $rating = preg_replace("/[^0-9.]/", "", $placeRating);
            $image = strstr($placeImage, '?', true) ?: $placeImage;
            $url = "https://www.kayak.com" . $placeURL;

            if ($image == null) return;

            $placeNames[] = $placeName;
            $placePrices[] = $price;
            $placeRatings[] = $rating;
            $placeImages[] = $image;
            $placeURLs[] = $url;

            $place = new Place();
            $place->name = $placeName;
            $place->price = $price;
            $place->image_path = $image;
            $place->booking_url = $url;

            $place->rating = $rating;
            $place->description = "Lorem Ipsum";
            $place->location_url = "https://example.com";
            $place->type = "Hotel";
            $place->bedrooms = 5;
            $place->bathrooms = 5;
            $place->max_guests = 2;

            $place->save();

        });
                

        // dd($placeImages);

    }
}
