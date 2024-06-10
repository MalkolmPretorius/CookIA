<!-- resources/views/pages/home.blade.php -->
@extends('templates.index')

@section('content')
    <div>
        <h1>My Home Page</h1>
        @inertia('Home')
    </div>
@endsection
