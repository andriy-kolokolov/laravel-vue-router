<?php

namespace Database\Seeders;

use App\Models\Cocktail;
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
        for ($i = 0; $i < 60; $i++) {
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
                        Cocktail::create([
                            'id' => $cocktail[0]['idDrink'], //string
                            'name' => $cocktail[0]['strDrink'],
                            'recipe' => $cocktail[0]['strInstructions'],
                            'image' => $cocktail[0]['strDrinkThumb'],
                            'alcoholic' => $cocktail[0]['strAlcoholic'],
                        ]);
                    }
                }
            }
        }
    }
}
