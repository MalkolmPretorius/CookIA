<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'recipe' => 'required|array',
            'recipe.titre' => 'required|string',
            'recipe.ingredients' => 'required|array',
            'recipe.etapes' => 'required|array',
        ]);

        $favorite = new Favorite();
        $favorite->user_id = Auth::id();
        $favorite->recipe = json_encode($request->recipe);
        $favorite->save();

        return response()->json(['message' => 'Recette ajoutée aux favoris !'], 200);
    }



    public function index()
    {
        $user = Auth::user();
        $favorites = Favorite::where('user_id', $user->id)->get();
        return view('favorite._index', compact('favorites'));
    }

    public function destroy($id)
    {
        $favorite = Favorite::find($id);
        if ($favorite->user_id == Auth::user()->id) {
            $favorite->delete();
        }
        return redirect()->route('favorite._index')->with('success', 'Recette supprimée des favoris.');
    }
}
