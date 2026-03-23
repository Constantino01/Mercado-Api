<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends BaseApiController
{
    public function __construct()
    {
        $this->model = Product::class;
        
        $this->regrasValidacao = [
            'category_id' => 'nullable|exists:categories,id',
            'product_name' => 'required|string|max:255',
            'product_cost' => 'required|numeric|min:0',
            'product_image' => 'nullable|url', // Trocar depois para a implementação da aws
            'product_description' => 'nullable|string',
            'product_min_quantity' => 'required|numeric|min:1|max:999',
            'unit_type' => 'required|string',
        ];
    }
}