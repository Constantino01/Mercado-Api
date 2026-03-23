<?php
namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends BaseApiController
{
    public function __construct()
    {
        $this->model = Category::class;
        
        $this->regrasValidacao = [
            'category_name' => 'required|string|max:255',
            'category_description' => 'nullable|string',
        ];
    }

    public function discounts()
    {
        // Confirma se o nome da tua tabela pivot é este
        return $this->belongsToMany(Discount::class, 'category__discounts', 'category_id', 'discount_id');
    }
}