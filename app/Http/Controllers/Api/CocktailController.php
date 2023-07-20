<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cocktail;
use Illuminate\Http\Request;

class CocktailController extends Controller {
    public function index() {
        $cocktails = Cocktail::paginate(12);
        return response()->json($cocktails);
    }

    public function show(Cocktail $cocktail) {

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

                        // Save the cocktail to the database
                        $cocktail->save();
                    }

                    // Retrieve the newly added cocktails from the database
                    $cocktails = Cocktail::where('name', 'LIKE', '%' . $searchTerm . '%')->get();
                }
            }
        }

        return response()->json(['data' => $cocktails]);
    }
}
