<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cocktail;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class CocktailController extends Controller {
    public function index() {
        $cocktails = Cocktail::paginate(12);
        return response()->json($cocktails);
    }

    public function show($id) {
        // Use Eloquent to find the cocktail by its ID in the database
        $cocktail = Cocktail::find($id);

        if (!$cocktail) {
            return response()->json(['error' => 'Cocktail not found'], 404);
        }

        return response()->json(['data' => $cocktail]);
    }

    public function random() {
        $cocktail = Cocktail::inRandomOrder()->first();

        return response()->json(['data' => $cocktail]);
    }

    public function search(Request $request) {
        $searchTerm = $request->input('name');

        // Use Eloquent to search for cocktails by name in the database
        $cocktails = Cocktail::where('name', 'LIKE', '%' . $searchTerm . '%')->get();

        // Check if any cocktail was found in the database
        if ($cocktails->isEmpty()) {
            // No cocktail found in the database, make a request to the API
            $apiUrl = 'https://www.thecocktaildb.com/api/json/v1/1/search.php?s=' . urlencode($searchTerm);
            $apiResponse = file_get_contents($apiUrl);

            // Check if the API call was successful and the response contains data
            if ($apiResponse !== false && !empty($apiResponse)) {
                $responseData = json_decode($apiResponse, true);
                // Check if the API response contains cocktails
                if (isset($responseData['drinks']) && !empty($responseData['drinks'])) {
                    // Iterate through the cocktails and add them to the database
                    foreach ($responseData['drinks'] as $apiCocktail) {
                        // Create a new Cocktail instance and set its attributes
                        $cocktail = new Cocktail();
                        $cocktail->id = $apiCocktail['idDrink'];
                        $cocktail->name = $apiCocktail['strDrink'];
                        $cocktail->recipe = $apiCocktail['strInstructions'];
                        $cocktail->image = $apiCocktail['strDrinkThumb'];
                        $cocktail->alcoholic = $apiCocktail['strAlcoholic'];

                        // Save the cocktail to the database
                        $cocktail->save();
                    }

                    // Handle the ingredients
                    for ($i = 1; $i <= 15; $i++) {
                        $ingredientKey = 'strIngredient' . $i;

                        $ingredientName = $apiCocktail[$ingredientKey];

                        if ($ingredientName) {
                            // Check if the ingredient already exists in the database
                            $existingIngredient = Ingredient::where('name', $ingredientName)->first();

                            if (!$existingIngredient) {
                                // If the ingredient is not found in the database, create a new record
                                $newIngredient = Ingredient::create([
                                    'name' => $ingredientName,
                                ]);
                                $ingredientId = $newIngredient->id;
                            } else {
                                $ingredientId = $existingIngredient->id;
                            }

                            // Now, sync the pivot table
                            $cocktail->ingredients()->attach($ingredientId);
                        }
                    }
                }
            }
        }

        $cocktails = Cocktail::where('name', 'LIKE', '%' . $searchTerm . '%')
            ->with('ingredients')
            ->get();

        return response()->json(['data' => $cocktails]);
    }

    public function searchByIngredient(Request $request) {
        // Get the ingredient name from the request
        $ingredientName = $request->input('name');

        // Search the ingredients table to find the ingredient by name
        $ingredient = Ingredient::where('name', $ingredientName)->first();

        if ($ingredient) {
            // If the ingredient is found, retrieve cocktails that include the ingredient
            $cocktails = $ingredient->cocktails()->get();
            return response()->json($cocktails);
        } else {
            // If the ingredient is not found
            return response()->json(['message' => 'Ingredient not found'], 404);
        }
    }
}
