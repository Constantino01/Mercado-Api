<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use SoftDeletes;
    protected $appends = ['is_active'];
    protected $fillable = ['discount_name', 'discount_value', 'discount_type', 'discount_start_date', 'discount_end_date'];
    
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product__discounts');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category__discounts');
    }

    public function getIsActiveAttribute()
    {
    $now = now();
    return $this->discount_start_date <= $now && $this->discount_end_date >= $now;
    }

    public function scopeActive($query)
    {
    return $query->where('discount_start_date', '<=', now())
                 ->where('discount_end_date', '>=', now());
    }

    public function discounts()
    {
        // Garante que o nome da tabela pivot está correto
        return $this->belongsToMany(Discount::class, 'category__discounts', 'category_id', 'discount_id');
    }
}