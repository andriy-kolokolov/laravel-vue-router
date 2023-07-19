<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cocktail;

class CocktailController extends Controller
{
    public function index() {
        $coctails = Cocktail::paginate(10);
        return response()->json($coctails);
    }

    public function show(Cocktail $cocktail) {

    }
}
