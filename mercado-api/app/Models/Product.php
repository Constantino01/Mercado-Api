<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Discount;

class Product extends Model
{
    use SoftDeletes;

    protected $appends = ['best_cost'];

    protected $fillable = [
        'category_id', 
        'product_name', 
        'product_cost', 
        'product_image',
        'product_description',
        'product_min_quantity',
        'unit_type'
    ];

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'product__discounts');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function getBestCostAttribute()
    {
        $basePrice = $this->product_cost;
        $finalPrice = $basePrice;

        // Carrega os descontos ativos (usando o scopeActive que criaste) do produto
        $productDiscounts = $this->discounts()->active()->get();

        // Carrega os descontos ativos da categoria deste produto
        $categoryDiscounts = $this->category ? $this->category->discounts()->active()->get() : collect();

        // Junta todos numa só lista
        $allActiveDiscounts = $productDiscounts->concat($categoryDiscounts);

        // Se não houver descontos, devolve o preço normal
        if ($allActiveDiscounts->isEmpty()) {
            return round($basePrice, 2);
        }

        // Testa todos os descontos válidos e guarda o preço mais baixo
        foreach ($allActiveDiscounts as $discount) {
            $calculatedPrice = $basePrice;

            if ($discount->discount_type === 'percent') {
                $calculatedPrice = $basePrice - ($basePrice * ($discount->discount_value / 100));
            } elseif ($discount->discount_type === 'fixed') {
                $calculatedPrice = $basePrice - $discount->discount_value;
            }

            // Impede que o preço fique negativo por erro
            $calculatedPrice = max(0, $calculatedPrice);

            if ($calculatedPrice < $finalPrice) {
                $finalPrice = $calculatedPrice;
            }
        }

        return round($finalPrice, 2);
    }
}