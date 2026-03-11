<?php

namespace App\Http\Controllers;

use App\Models\Discount; 
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index()
    {
        return response()->json(Discount::all());
    }

    public function store(Request $request)
    {
        $discount = Discount::create($request->all());
        return response()->json($discount, 201);
    }

    public function show($id)
    {
        return response()->json(Discount::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $discount = Discount::findOrFail($id);
        $discount->update($request->all());
        return response()->json($discount);
    }

    public function destroy($id)
    {
        $discount = Discount::findOrFail($id);
        $discount->delete(); 
        return response()->json(['message' => 'Desconto apagado com sucesso!']);
    }

    public function applyToProducts(Request $request, $id) //It receives an array of products, so it can handle 1 or many
    {
        $discount = Discount::findOrFail($id);
        $discount->products()->sync($request->product_ids); //Sees the relation in the model and syncs the new data
        
        return response()->json(['message' => 'Desconto Aplicado Aos Produtos Com Sucesso!']);
    }

    public function applyToCategories(Request $request, $id) //It receives an array of products, so it can handle 1 or many
    {
        $discount = Discount::findOrFail($id);
        $discount->products()->sync($request->product_ids); //Sees the relation in the model and syncs the new data
        
        return response()->json(['message' => 'Desconto Aplicado Aos Produtos Com Sucesso!']);
    }
}
