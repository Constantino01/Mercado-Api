<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Cetegory;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'product_name',
        'product_image',
        'product_cost',
    ];

   public function discounts()
   {
        return $this->belongsToMany(Discount::class, 'products__discounts');
   }
}

