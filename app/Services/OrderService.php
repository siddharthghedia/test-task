<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\StockOfIngredient;
use App\Events\IngredientsStockHalf;
use App\Models\Order;

class OrderService
{
    /**
     * Accepts the order details from the request payload.
     */
    public function acceptOrderDetail($products, $request): array
    {
        $attachedArray = [];
        foreach ($request->get('products') as $product) {
            if (isset($attachedArray[$product['product_id']])) {
                $attachedArray[$product['product_id']]['quantity' ] = $attachedArray[$product['product_id']]['quantity' ] + $product['quantity'];
            } else {
                $attachedArray[$product['product_id']] = ['quantity' => $product['quantity']];
            }
        }

        $errors = [];
        foreach ($products as $product) {
            $ingredients = $product->ingredients;
            foreach ($ingredients as $ingredient) {
                if ($ingredient->weight < (abs($ingredient->stocks_sum_stock) + $attachedArray[$product->id]['quantity'] * $ingredient->pivot->consumed_weight)) {
                    array_push($errors, 'product_id of ' . $product->id . ' is out of stock.'); 
                    unset($attachedArray[$product->id]);
                    $products = $products->reject(function($oneProduct) use ($product) {
                        return $oneProduct->id == $product->id;
                    });
                    break;
                }
            }
        }

        return [
            'attachedArray' => $attachedArray,
            'products' => $products,
            'errors' => $errors
        ];
    }

    /**
     * Save Order Detail to database.
     */
    public function saveOrder($attachedArray): void
    {
        $order = Order::create([
            'address' => 'ABC PQR, 309090'
        ]);

        $order->products()->attach($attachedArray);
    }

    /**
     * Update Stock and send mail to merchant.
     */
    public function stockUpdate($products, $attachedArray): void
    {
        foreach ($products as $product) {
            $product->load('ingredients');
            $ingredients = $product->ingredients;
            foreach ($ingredients as $ingredient) {
                $ingredient->load('stocks')->loadSum('stocks', 'stock');
                // Updates the stock of the ingredients.
                $stockOfIngredient = StockOfIngredient::create([
                    'ingredient_id' => $ingredient->id,
                    'stock' => - $attachedArray[$product->id]['quantity'] * $ingredient->pivot->consumed_weight
                ]);

                if ($ingredient->stocks->isEmpty()) {
                    if ($this->getStockInPercentage($ingredient, $stockOfIngredient) >= Ingredient::HALF_STOCK_LEVEL) {
                        $this->updateFlagAndSendMail($stockOfIngredient, $ingredient);
                    }
                }

                if ($ingredient->stocks->isNotEmpty() && !$ingredient->stocks->where('mail_sent_for_half_level', 1)->last()) {
                    if ($this->getStockInPercentage($ingredient, $stockOfIngredient) >= Ingredient::HALF_STOCK_LEVEL) {
                        $this->updateFlagAndSendMail($stockOfIngredient, $ingredient);
                    }
                }
            }
        }
    }

    public function getStockInPercentage($ingredient, $stockOfIngredient)
    {
        return (abs($ingredient->stocks_sum_stock + $stockOfIngredient->stock) * 100)/$ingredient->weight;
    }

    public function updateFlagAndSendMail($stockOfIngredient, $ingredient)
    {
        $stockOfIngredient->mail_sent_for_half_level = true;
        $stockOfIngredient->save();
        IngredientsStockHalf::dispatch($ingredient);
    }
}
