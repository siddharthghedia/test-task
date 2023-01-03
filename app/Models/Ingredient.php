<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $guarded = [];

    public const HALF_STOCK_LEVEL = 50;

    public function stocks()
    {
        return $this->hasMany(StockOfIngredient::class, 'ingredient_id', 'id');
    }
}
