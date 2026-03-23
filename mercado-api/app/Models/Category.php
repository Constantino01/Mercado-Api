<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Discount;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = ['category_name', 'category_description'];

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'category__discounts');
    }
}