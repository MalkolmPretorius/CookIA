@extends('templates.index')

@section('title')
    Mes Favoris
@stop

@section('content')
@php
$user = auth()->user(); 
$favorites = $user->favorites()->orderBy('recipe', 'ASC')->get();
@endphp
<div class="container mx-auto pt-4 pl-4 pr-4 pb-12">
    <h1 class="text-4xl font-bold text-center mb-8">
        Mes recettes favorites
    </h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
        <!-- Iterate through the favorite recipes -->
        @foreach($favorites as $favorite)
            @php
                $recipe = $favorite->decoded_recipe;
            @endphp
            <article class="relative myheader rounded-lg shadow-lg hover:shadow-2xl transition-shadow duration-300 monster-card">
                <div class="p-4">
                        <h1 class="text-red-500 text-xl mb-4 ">Recette : {{ $recipe['titre'] }}</h1>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-lg text-blue-500">Ingrédients:</span>
                        </div>
                        <ul class="list-disc pl-5">
                            @foreach($recipe['ingredients'] as $ingredients)
                                <li class="mb-2">{{ $ingredients }}</li>
                            @endforeach
                        </ul>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-lg text-blue-500">Instructions:</span>
                    </div>
                    <ul class="list-disc pl-5">
                        @foreach($recipe['etapes'] as $etape)
                            <li class="mb-2">{{ $etape }}</li>
                        @endforeach
                    </ul>
                    <form action="{{ route('favorites.destroy', $favorite->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="mt-4 bg-red-500 text-white px-3 py-2  rounded-lg">Supprimer des favoris</button>
                    </form>
                </div>
            </article>
        @endforeach
    </div>
</div>
@stop
