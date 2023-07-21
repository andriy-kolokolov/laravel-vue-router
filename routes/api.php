<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\CocktailController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('cocktails', [CocktailController::class, 'index'])->name('api.cocktails.index');
Route::get('cocktails/search', [CocktailController::class, 'search'])->name('api.cocktails.search');
Route::get('cocktails/searchByIngredient', [CocktailController::class, 'searchByIngredient'])->name('api.cocktails.searchByIngredient');
Route::get('cocktails/random', [CocktailController::class, 'random'])->name('api.cocktails.random');
Route::get('cocktails/{id}', [CocktailController::class, 'show'])->name('api.cocktails.show');

Route::post('leads/', [LeadController::class, 'store'])->name('api.leads.store');
