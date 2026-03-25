<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_email',
        'client_number',
        'order_cost',
        'order_status',
        'order_code'
    ];

    // Ligação aos Produtos através da tabela pivot
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order__products')
                    ->withPivot('id', 'quantity', 'charged_cost')
                    ->withTimestamps();
    }
}