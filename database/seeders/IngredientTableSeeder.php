<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Seeder;

class IngredientTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ingredients = [
            [
                'name' => 'Beef',
                'weight' => 20000 // in gram
            ],
            [
                'name' => 'Cheese',
                'weight' => 5000
            ],
            [
                'name' => 'Onion',
                'weight' => 1000
            ]
        ];

        foreach ($ingredients as $ingredient) {
            Ingredient::create([
                'name' => $ingredient['name'],
                'weight' => $ingredient['weight']
            ]);
        }
    }
}
