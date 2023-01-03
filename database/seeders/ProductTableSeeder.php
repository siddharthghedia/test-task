<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = [
            [
                'name' => 'Burger',
                'price' => 50,
                'ingredients' => [
                    [
                        'name' => 'Beef',
                        'weight' => 150
                    ],
                    [
                        'name' => 'Cheese',
                        'weight' => 30
                    ],
                    [
                        'name' => 'Onion',
                        'weight' => 20
                    ]
                ]
            ],
            [
                'name' => 'Bread',
                'price' => 5,
                'ingredients' => [
                    [
                        'name' => 'Beef',
                        'weight' => 50
                    ],
                    [
                        'name' => 'Cheese',
                        'weight' => 10
                    ]
                ]
            ],
            [
                'name' => 'Pizza Base',
                'price' => 30,
                'ingredients' => [
                    [
                        'name' => 'Beef',
                        'weight' => 100
                    ],
                    [
                        'name' => 'Cheese',
                        'weight' => 20
                    ]
                ]
            ]
        ];

        foreach ($products as $product) {
            $dbProduct = Product::create([
                'name' => $product['name'],
                'price' => $product['price']
            ]);
            $attachIngredientsArray = [];
            foreach ($product['ingredients'] as $ingredient) {
                $dbIngredient = Ingredient::where('name', $ingredient['name'])->first();
                $attachIngredientsArray[$dbIngredient->id] = ['consumed_weight' => $ingredient['weight']];
            }
            $dbProduct->ingredients()->attach($attachIngredientsArray);
        }
    }
}
