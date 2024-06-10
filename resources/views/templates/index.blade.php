<!DOCTYPE html>
<html lang="fr">

<head>
    @include('templates.partials._head')
</head>

<body class="bg-gray-800 text-white font-sans">
    @if (request()->is('favorite')||request()->is('profile'))
        @include('templates.partials._header')
    @endif

    @include('templates.partials._main')

    @if (request()->is('favorite')||request()->is('profile'))
        @include('templates.partials._footer')
    @endif
</body>

