<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Monster;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MonsterController extends Controller
{
    // public function index(){
    //     $monster= Monster::first();
    // }

    public function addToFavorites($monsterId)
    {
        $user = auth()->user();
        $favorite = Favorite::where('user_id', $user->id)->where('monster_id', $monsterId);
        if ($favorite->exists()) {
            $favorite->delete();
        } else {
            Favorite::create(['user_id' => $user->id, 'monster_id' => $monsterId]);
        }
        return redirect()->route('deck._index');
    }
    public function create()
    {
        $types = Type::all();
        $rarityIds = Monster::distinct('rarety_id')->pluck('rarety_id');
        $rarities = Rarety::whereIn('id', $rarityIds)->pluck('name', 'id');

        return view('monsters.addMonsters', compact('types', 'rarities'));
    }

    public function store(Request $request)
    {

        $monster = new Monster();
        $monster->name = $request->input('name');
        $monster->user_id = auth()->user()->id;
        $monster->type_id = $request->input('type');
        $monster->rarety_id = $request->input('rarety');
        $monster->pv = $request->input('pv');
        $monster->attack = $request->input('attack');
        $monster->defense = $request->input('defense');
        $monster->description = $request->input('description');
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $monster->image_url = $imageName;
        }


        $monster->save();

        return redirect()->route('monsters.show', [
            'monster' => $monster->id,
            'slug' => \Illuminate\Support\Str::slug($monster->name, '-'),
        ]);
    }

    public function edit(Monster $monster)
    {
        $this->authorize('update', $monster);

        $user = Auth::user();
        $types = Type::all();
        $rarityIds = Monster::distinct('rarety_id')->pluck('rarety_id');
        $rarities = Rarety::whereIn('id', $rarityIds)->pluck('name', 'id');


        return view('monsters.editMonsters', compact('monster', 'user', 'types', 'rarities'));
    }
    public function updateMonster(Request $request, $monster)
    {

        $monster = Monster::findOrFail($monster);

        $monster->name = $request->input('name');
        $monster->type_id = $request->input('type');
        $monster->rarety_id = $request->input('rarety');
        $monster->pv = $request->input('pv');
        $monster->attack = $request->input('attack');
        $monster->defense = $request->input('defense');
        $monster->description = $request->input('description');

        if ($request->hasFile('image')) {
            if ($monster->image_url) {
                Storage::delete('public/images/' . $monster->image_url);
            }

            $imagePath = $request->file('image')->store('public/images');
            $monster->image_url = basename($imagePath);
        }

        $monster->save();

        return redirect()->route('monsters.show', ['monster' => $monster->id, 'slug' => \Illuminate\Support\Str::slug($monster->name, '-')]);
    }

    public function deleteMonster(Monster $monster)
    {
        $this->authorize('delete', $monster);

        $comments = Comment::where('monster_id', $monster->id)->get();

        foreach ($comments as $comment) {
            $comment->delete();
        }

        $notations = Notation::where('monster_id', $monster->id)->get();

        foreach ($notations as $notation) {
            $notation->delete();
        }

        $favorites = Favorite::where('monster_id', $monster->id)->get();

        foreach ($favorites as $favorite) {
            $favorite->delete();
        }

        $monster->delete();

        return redirect()->route('pages.home');
    }

    public function rate(Request $request, $monsterId)
    {
        $userId = auth()->user()->id;
            $notation = Notation::updateOrCreate(
            ['user_id' => $userId, 'monster_id' => $monsterId],
            ['notation' => $request->input('rating')]
        );

        return back();
    }
}
