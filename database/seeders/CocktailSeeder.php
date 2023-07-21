<?php

namespace Database\Seeders;

use App\Models\Cocktail;
use App\Models\Ingredient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CocktailSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $apiUrl = 'https://www.thecocktaildb.com/api/json/v1/1/random.php';
        for ($i = 0; $i < 65; $i++) {
            $jsonString = file_get_contents($apiUrl);

            if ($jsonString !== false) {
                $drinks = json_decode($jsonString, true);

                if ($drinks === null) {
                    echo "Error decoding JSON: " . json_last_error_msg();
                } else {
                    $cocktail = $drinks['drinks'];
                    // Check if the cocktail ID already exists in the database
                    $existingCocktail = Cocktail::where('id', $cocktail[0]['idDrink'])->first();

                    if (!$existingCocktail) {
                        // If the cocktail ID is not found in the database, create a new record
                        $newCocktail = Cocktail::create([
                            'id' => $cocktail[0]['idDrink'],
                            'name' => $cocktail[0]['strDrink'],
                            'recipe' => $cocktail[0]['strInstructions'],
                            'image' => $cocktail[0]['strDrinkThumb'],
                            'alcoholic' => $cocktail[0]['strAlcoholic'],
                        ]);

                        // Now, let's handle the ingredients
                        for ($j = 1; $j <= 15; $j++) {
                            $ingredientKey = 'strIngredient' . $j;

                            $ingredientName = $cocktail[0][$ingredientKey];

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
                                $newCocktail->ingredients()->attach($ingredientId);
                            }
                        }
                    }
                }
            }
        }
    }
}
