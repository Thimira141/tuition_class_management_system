@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content-c1')

    <div class="container my-5">
        <div class="row justify-content-around">
            <div class="col-md-3 col-sm-12 p-2 mb-3 bg-body-tertiary shadow">
                <h3>Upcoming Events</h3>
                <hr>
                <!-- Some borders are removed -->
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Item</li>
                    <li class="list-group-item">Item</li>
                    <li class="list-group-item">Item</li>
                    <li class="list-group-item">Item</li>
                    <li class="list-group-item">Item</li>
                </ul>
            </div>
            <div class="col-md-3 col-sm-12 p-2 mb-3 bg-body-tertiary shadow d-flex flex-column">
                <h3>Notes</h3>
                <hr>
                <!-- Make textarea flex-grow to fill remaining space -->
                <textarea class="form-control flex-grow-1" placeholder="Write your notes here..."></textarea>
            </div>

        </div>
    </div>


@endsection
