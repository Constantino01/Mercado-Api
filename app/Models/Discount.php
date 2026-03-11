<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Product;
use App\Models\Category;

class Discount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'discount_name',
        'discount_type',
        'discount_value',
        'discount_begin',
        'discount_end',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'products__discounts');
    }

   public function categories()
   {
        return $this->belongsToMany(Category::class, 'category__discounts');
   }
}
