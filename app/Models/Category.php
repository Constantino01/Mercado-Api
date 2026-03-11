<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_name',
        'category_unit',
    ];

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'category__discounts');
    }
}
