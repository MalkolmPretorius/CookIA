@extends('templates.index')

@section('title')
    Ajouter un monstres
@stop

@section('content')





    <div class="container mx-auto pb-12">
        <div class="flex flex-wrap justify-center">
            <div class="w-full">
                <div class="bg-gray-700 p-6 rounded-lg shadow-lg">
                    <h2 class="text-2xl font-bold mb-4 text-center creepster">
                        Modifier un Monstre
                    </h2>
                    <form action="{{ route('monsters.updateMonster', ['monster' => $monster->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div>
                            <label for="name" class="block mb-1">Nom du monstre</label>
                            <input type="text" id="name" name="name"
                                class="w-full border rounded px-3 py-2 text-gray-700" value="{{ $monster->name }}" />
                        </div>

                        <div>
                            <label for="type" class="block mb-1">Type</label>
                            <!-- Assumez que $types est une collection ou un tableau d'options de types -->
                            <select id="type" name="type" class="w-full border rounded px-3 py-2 text-gray-700">
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="rarety" class="block mb-1">Rareté</label>
                            <select id="rarety" name="rarety" class="w-full border rounded px-3 py-2 text-gray-700">
                                @foreach ($rarities as $rarityId => $rarityName)
                                    <option value="{{ $rarityId }}">{{ $rarityName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="pv" class="block mb-1">PV</label>
                            <input type="text" id="pv" name="pv"
                                class="w-full border rounded px-3 py-2 text-gray-700" value="{{ $monster->pv }}" />
                        </div>

                        <div>
                            <label for="attack" class="block mb-1">Attaque</label>
                            <input type="text" id="attack" name="attack"
                                class="w-full border rounded px-3 py-2 text-gray-700" value="{{ $monster->attack }}" />
                        </div>

                        <div>
                            <label for="defense" class="block mb-1">Défense</label>
                            <input type="text" id="defense" name="defense"
                                class="w-full border rounded px-3 py-2 text-gray-700" value="{{ $monster->defense }}" />
                        </div>

                        <div>
                            <label for="description" class="block mb-1">Description</label>
                            <textarea id="description" name="description" class="w-full border rounded px-3 py-2 text-gray-700">{{ $monster->description }}</textarea>
                        </div>

                        <div>
                            <label for="image" class="block mb-1">Image</label>
                            <input type="file" id="image" name="image" class="w-full border rounded px-3 py-2 text-gray-700" value="{{$monster->image_url}}" accept="*"  />
                        </div>

                        <div class="flex justify-center items-center">
                            <button type="submit"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Modifier le monstre
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop
