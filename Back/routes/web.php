<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecipeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('pages.home');
})->middleware('auth')->name('pages.home');

Route::post('/recipes/{recipeId}/favorite', [RecipeController::class, 'addToFavorites'])->middleware(['auth', 'verified'])->name('recipes.favorite');

Route::post('/recipes/{comment}/delete', [CommentController::class, 'delete'])->middleware(['auth', 'verified'])->name('comments.delete');

Route::get('/addRecipe', [RecipeController::class, 'create'])->middleware(['auth', 'verified'])->name('recipes.addRecipes');

Route::post('/recipes', [RecipeController::class, 'store'])->middleware(['auth', 'verified'])->name('recipes.store');

Route::get('/recipes/{recipe}/editRecipe', [RecipeController::class, 'edit'])->middleware(['auth', 'verified'])->name('recipes.editRecipes');

Route::post('/recipes/{recipe}/update', [RecipeController::class, 'updateRecipe'])->middleware(['auth', 'verified'])->name('recipes.updateRecipe');

Route::delete('/recipes/{recipe}/delete', [RecipeController::class, 'deleteRecipe'])->middleware(['auth', 'verified'])->name('recipes.deleteRecipe');

Route::get('/favorite', function () {
    return view('favorite._index');
})->name('favorite._index');

Route::get('/recipes', function () {
    return view('recipes.index');
})->name('recipes.index');

Route::get('/recipes/{recipe}/{slug}', function (\App\Models\Recipe $recipe) {
    return view('recipes.show', compact('recipe'));
})->name('recipes.show');

Route::get('/user', function () {
    return view('users.index');
})->name('users.index');

Route::get('/users/{user}/{slug}', function (\App\Models\User $user) {
    return view('users.show', compact('user'));
})->name('users.show');

Route::get('/dashboard', function () {
    return view('pages.home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
