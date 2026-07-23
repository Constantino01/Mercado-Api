<?php
namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends BaseApiController
{
    public function __construct()
    {
        $this->model = Discount::class;
        
        $this->regrasValidacao = [
            'discount_name' => 'required|string|max:255',
            'discount_value' => 'required|numeric|min:0',
            'discount_type' => 'required|in:percent,fixed',
            'discount_start_date' => 'required|date|after_or_equal:today',
            'discount_end_date' => 'required|date|after_or_equal:discount_start_date',
        ];
    }

    public function syncItems(Request $request, $id)
    {
        $discount = $this->model::findOrFail($id);
        
        // O sync() apaga os antigos e insere os novos automaticamente
        if ($request->has('products')) {
            $discount->products()->sync($request->products);
        }
        
        if ($request->has('categories')) {
            $discount->categories()->sync($request->categories);
        }
        
        return response()->json(['message' => 'Associações atualizadas com sucesso!']);
    }

    public function show($id)
    {
        // O "with" obriga o Laravel a carregar as relações e enviá-las no JSON para o React
        $discount = $this->model::with(['products', 'categories'])->findOrFail($id);
        
        return response()->json($discount);
    }

    /**
     * Remove o desconto permanentemente da base de dados.
     */
    public function forceDelete($id)
    {
        // O withTrashed() é obrigatório para encontrar itens que já estão na lixeira
        $discount = Discount::withTrashed()->find($id);

        if (!$discount) {
            return response()->json(['message' => 'Desconto não encontrado.'], 404);
        }

        // Apaga de vez (remove a linha da tabela)
        $discount->forceDelete();

        return response()->json(['message' => 'Desconto apagado permanentemente com sucesso!']);
    }

    /**
     * Restaura um desconto que estava na lixeira.
     */
    public function restore($id)
    {
        $discount = Discount::withTrashed()->find($id);

        if (!$discount) {
            return response()->json(['message' => 'Desconto não encontrado.'], 404);
        }

        $discount->restore();

        return response()->json(['message' => 'Desconto restaurado com sucesso!']);
    }
}