@extends('templates.index')

@section('title')
    {{ $monster->name }}
@stop

@section('content')
    @php
        $user = auth()->user();
        if ($user) {
            $favorites = $user
                ->favorites()
                ->orderBy('monster_id', 'ASC')
                ->get();
            $favoriteMonsterId = $user
                ->favorites()
                ->pluck('monster_id')
                ->toArray();
        } else {
            $favoriteMonsterId = [];
        }

    @endphp

    <section class="mb-20">
        <div class="bg-gray-700 rounded-lg shadow-lg monster-card" data-monster-type="{{ $monster->type->name }}">
            <div class="md:flex">
                <!-- Image du monstre -->
                <div class="w-full md:w-1/2 relative">
                    <img class="w-full h-full object-cover rounded-t-lg md:rounded-l-lg md:rounded-t-none"
                        src="{{ asset('images/' . $monster->image_url) }}" alt="{{ $monster->name }}" />
                    <div class="absolute top-4 right-4">
                        <form action="{{ route('monsters.favorite', ['monsterId' => $monster->id]) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="text-white rounded-full p-2 transition-colors duration-300
                      {{ in_array($monster->id, $favoriteMonsterId) ? 'bg-red-700 hover:bg-red-900' : 'bg-gray-400 hover:bg-red-700' }}"
                                style="width: 40px; height: 40px; display: flex; justify-content: center; align-items: center;">
                                <i class="fa fa-bookmark"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Détails du monstre -->
                <div class="p-6 md:w-1/2">
                    <h2 class="text-3xl font-bold mb-2 creepster">
                        {{ $monster->name }}
                    </h2>
                    <p class="text-gray-300 text-sm mb-4">
                        {{ $monster->description }}
                    </p>
                    <div class="mb-4">
                        <strong class="text-white">Créateur:</strong>
                        <span class="text-red-400">{{ $monster->user->name }}</span>
                    </div>
                    <div class="mb-4">
                        <div>
                            <strong class="text-white">Type:</strong>
                            <span class="text-gray-300">{{ $monster->type->name }}</span>
                        </div>
                        <div>
                            <strong class="text-white">PV:</strong>
                            <span class="text-gray-300">{{ $monster->pv }}</span>
                        </div>
                        <div>
                            <strong class="text-white">Attaque:</strong>
                            <span class="text-gray-300">{{ $monster->attack }}</span>
                        </div>
                        <div>
                            <strong class="text-white">Défense:</strong>
                            <span class="text-gray-300">{{ $monster->defense }}</span>
                        </div>
                    </div>
                    <div class="mb-4">
                        @php
                            $averageRating = $monster->notations->avg('notation');
                            $fullStars = floor($averageRating);
                            $halfStar = round($averageRating - $fullStars, 1) >= 0.5;
                        @endphp

                        @for ($i = 0; $i < $fullStars; $i++)
                            <i class="fa fa-star text-yellow-400"></i>
                        @endfor

                        @if ($halfStar)
                            <i class="fa fa-star-half text-yellow-400"></i>
                            @php $fullStars++; @endphp
                        @endif

                        @for ($i = $fullStars; $i < 5; $i++)
                            <i class="fa fa-star text-gray-300"></i>
                        @endfor

                        <span class="text-gray-300 text-sm">({{ number_format($averageRating, 1) }}/5.0)</span>
                    </div>
                    <div class="flex space-x-6">
                    @can('update', $monster)
                        <a href="{{ route('monsters.editMonsters', ['monster' => $monster]) }}"
                            class="text-white bg-red-500 hover:bg-red-700 rounded-full px-4 py-2 transition-colors duration-300">
                            Modifier le monstre
                        </a>
                    @endcan

                    @can('delete', $monster)
                        <form action="{{ route('monsters.deleteMonster', ['monster' => $monster->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')

                            <!-- Bouton de suppression -->
                            <button type="submit" class="text-white bg-red-500  hover:bg-red-700 rounded-full px-4 py-2 transition-colors duration-300">
                                Supprimer le monstre
                            </button>
                        </form>
                    @endcan

                    </div>
                </div>
            </div>
        </div>
    </section>
    @auth
       <!-- Section d'évaluation -->
       <div class="mt-6">
        <h3 class="text-2xl font-bold mb-4">Évaluez ce Monstre</h3>
        <form action="{{ url("/monsters/{$monster->id}/rate") }}" method="POST">
            @csrf
            <div id="rating-section" class="flex items-center">
                @for ($i = 1; $i <= 5; $i++)
                    <button type="submit" name="rating" value="{{ $i }}" class="rating-star text-5xl"
                        onmouseover="highlightStars({{ $i }})" onmouseout="resetStars()">
                        &#9733;
                    </button>
                @endfor
            </div>
        </form>

    </div>
    <script>
        function highlightStars(value) {
            const stars = document.querySelectorAll('.rating-star');
            stars.forEach((star, index) => {
                if (index < value) {
                    star.classList.add('text-yellow-500');
                } else {
                    star.classList.remove('text-yellow-500');
                }
            });
        }

        function resetStars() {
            const stars = document.querySelectorAll('.rating-star');
            stars.forEach(star => {
                star.classList.remove('text-yellow-500');
            });
        }
    </script>

        <!-- Section commentaires -->
        <div class="mt-6">
            <h3 class="text-2xl font-bold mb-4">Commentaires</h3>
            <div id="commentaires-section">
                <!-- Commentaire 1 -->
                @foreach ($monster->comments as $comment)
                    <div class="mb-4 bg-gray-800 rounded p-4">
                        <p class="font-bold text-red-400">{{ $comment->user->name }}</p>
                        <p class="text-sm text-gray-400">{{ $comment->created_at }}</p>
                        <p class="text-gray-300 mt-2">
                            {{ $comment->content }}
                        </p>
                        @can('delete', $comment)
                            <form action="{{ route('comments.delete', ['comment' => $comment]) }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" class="text-red-500 hover:underline">Supprimer le commentaire</button>
                            </form>
                        @endcan
                    </div>
                @endforeach

            </div>
            <!-- Formulaire de commentaire -->
            <div class="bg-gray-800 rounded p-4">
                <h4 class="font-bold text-lg text-red-500 mb-2">Laissez un commentaire</h4>
                <form action="{{ route('comments.store', ['monster' => $monster->id]) }}" method="POST">
                    @csrf
                    <textarea name="content" class="w-full p-2 bg-gray-900 rounded text-gray-300" rows="4"
                        placeholder="Votre commentaire..."></textarea>
                    <button type="submit"
                        class="mt-2 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full w-full">
                        Envoyer
                    </button>
                </form>
            </div>

        </div>
    @endauth
    </section>
@stop
