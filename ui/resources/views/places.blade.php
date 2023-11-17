@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/welive.css') }}">

<div class="container">
    <div class="row">
        @foreach($places as $place)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow">
                    <img src="{{ asset('images/' . $place->image_path) }}" class="card-img-top" alt="{{ $place->name }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $place->name }}</h5>
                        <p class="card-text">
                            <i class="fas fa-building"></i>&nbsp; <strong>Location:</strong> <a href="{{ $place->location_url }}" target="_blank">{{ $place->location_url }}</a><br><br>
                            <strong>Type:</strong> {{ $place->type }}<br>
                            <i class="fas fa-bed"></i>&nbsp; <strong>Bedrooms:</strong> {{ $place->num_bedrooms }}<br>
                            <i class="fas fa-shower"></i>&nbsp; <strong>Bathrooms:</strong> {{ $place->num_bathrooms }}<br>
                            <i class="fas fa-user-friends"></i>&nbsp; <strong>Max Guests:</strong> {{ $place->max_guests }}<br>
                            <i class="fas fa-money-bill-wave"></i>&nbsp; <strong>Price:</strong> {{ $place->price }}<br>
                        </p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ $place->booking_url }}" class="btn btn-primary btn-block" target="_blank">Book Now</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
