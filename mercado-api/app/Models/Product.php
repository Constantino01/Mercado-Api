<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 
        'product_name', 
        'product_cost', 
        'product_image'
    ];

    //Criar a relação muitos para muitos com a tabela order__products
}