@extends('layouts.app')

@section('css')
    <!-- Owl-carousel css -->
    <link href="{{ URL::asset('assets/plugins/owl-carousel/owl.carousel.css') }}" rel="stylesheet" />
    <!-- Maps css -->
    <link href="{{ URL::asset('assets/plugins/jqvmap/jqvmap.min.css') }}" rel="stylesheet">
@endsection
@section('content')
    <div style="display: flex; align-items: center; justify-content: center;height: 80vh;">
        <div class="soon-content">
            قريبا ....
        </div>
    </div>
@endsection
