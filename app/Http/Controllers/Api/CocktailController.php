<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cocktail;

class CocktailController extends Controller
{
    public function index() {
        $cocktails = Cocktail::paginate(12);
        return response()->json($cocktails);
    }

    public function show(Cocktail $cocktail) {

    }
}
