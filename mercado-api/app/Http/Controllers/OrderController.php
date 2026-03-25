<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Order::with('products')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validar APENAS os dados do cliente e a lista de items
        $validated = $request->validate([
            'client_email' => 'required|email|max:255',
            'client_number' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantidade' => 'required|numeric|min:0.001',
        ]);

        try {
            DB::beginTransaction();

            $itemIds = collect($validated['items'])->pluck('id');
            $produtosReais = Product::whereIn('id', $itemIds)->get()->keyBy('id');

            $custoTotalEncomenda = 0;
            $pivotData = [];

            foreach ($validated['items'] as $item) {
                $produtoDB = $produtosReais->get($item['id']);
                
                $precoAtivo = $produtoDB->best_cost ?: $produtoDB->product_cost;
                $divisor = $produtoDB->unit_type === 'kg' ? 1000 : 1;
                $custoLinha = ($precoAtivo / $divisor) * $item['quantidade'];

                $custoTotalEncomenda += $custoLinha;

                $pivotData[$produtoDB->id] = [
                    'quantity' => $item['quantidade'],
                    'charged_cost' => $custoLinha,
                ];
            }

            // 4. Criar a Encomenda (com o order_status e order_code corretos)
            $order = Order::create([
                'client_email' => $validated['client_email'],
                'client_number' => $validated['client_number'],
                'order_cost' => $custoTotalEncomenda,
                'order_status' => 'Pendente',
                'order_code' => $this->generateUniqueOrderCode(), // <--- Sintaxe corrigida aqui
            ]);

            $order->products()->attach($pivotData);

            DB::commit();

            return response()->json([
                'message' => 'Encomenda criada com sucesso!',
                'order_id' => $order->id,
                'order_code' => $order->order_code, // <--- Adicionado para o React ler
                'total_charged' => $custoTotalEncomenda
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao processar a encomenda.', 
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return response()->json($order->load('products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'client_email' => 'sometimes|email|max:255',
            'client_number' => 'sometimes|string|max:255',
            'order_cost' => 'sometimes|numeric|min:0',
            'order_status' => 'sometimes|string|max:255',
        ]);

        $order->update($validated);
        return response()->json($order);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(['message' => 'Encomenda Apagada Com Sucesso']);
    }

    private function generateUniqueOrderCode(): string
    {
        $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $length = 8;
        $code = '';

        do {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[random_int(0, strlen($characters) - 1)];
            }
            // Corrigido de order_hash para order_code
        } while (Order::where('order_code', $code)->exists());

        return $code;
    }
}