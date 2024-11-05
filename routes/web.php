<?php

use Inertia\Inertia;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FavoriteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConversationController;
use App\Models\Conversation;

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
    return Inertia::render('ChatBox');
})->middleware('auth')->name('pages.home');

// Route::get('/dashboard', function () {
//     return Inertia::render('ChatBox');
// })->middleware(['auth', 'verified'])->name('dashboard');


Route::post('/favorites', [FavoriteController::class, 'store'])->middleware(['auth', 'verified'])->name('favorites.store');
Route::get('/favorites', [FavoriteController::class, 'index'])->middleware('auth')->name('favorites.index');
Route::delete('/favorites/{id}', [FavoriteController::class, 'destroy'])->middleware('auth')->name('favorites.destroy');


Route::post('/conversation/store', [ConversationController::class, 'store'])->middleware('auth');







Route::get('/favorite', function () {
    return view('Favorite._index');
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



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
