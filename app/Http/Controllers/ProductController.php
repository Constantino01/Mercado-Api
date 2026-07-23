<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends BaseApiController
{
        public function index()
    {
        // Devolve apenas 24 produtos de cada vez
        return Product::paginate(24); 
    }

    public function __construct()
    {
        $this->model = Product::class;

        $this->regrasValidacao = [
            'category_id' => 'nullable|exists:categories,id',
            'product_name' => 'required|string|max:255',
            'product_cost' => 'required|numeric|min:0',
            'imagem' => 'nullable|image|max:5120', // Valida se é imagem até 5MB
            'product_description' => 'nullable|string',
            'product_min_quantity' => 'required|numeric|min:1|max:999',
            'unit_type' => 'required|string',
        ];
    }

    public function store(Request $request)
    {
        // ... (validações do BaseApiController devem estar a ser chamadas algures)
        $dados = $request->all();

        if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
            // Guarda a imagem no S3, na pasta 'produtos' com visibilidade pública
            $caminho = $request->file('imagem')->store('produtos', 's3');
            
            // Rede de segurança
            if (!$caminho) {
                return response()->json(['message' => 'Erro de comunicação ao enviar a imagem para a AWS S3.'], 500);
            }

            // Gera a URL pública para guardar na Base de Dados
            $dados['product_image'] = Storage::disk('s3')->url($caminho);
        }

        $produto = Product::create($dados);

        return response()->json($produto, 201);
    }

    public function update(Request $request, $id)
    {
        $produto = Product::findOrFail($id);
        $dados = $request->all();

        if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
            
            // 1. Guarda a NOVA imagem
            $caminhoNovo = $request->file('imagem')->store('produtos', 's3');

            // Rede de segurança
            if (!$caminhoNovo) {
                return response()->json(['message' => 'Erro de comunicação ao enviar a imagem para a AWS S3.'], 500);
            }

            // Atualiza os dados com a NOVA URL
            $dados['product_image'] = Storage::disk('s3')->url($caminhoNovo);

            // 2. Apaga a imagem ANTIGA do S3 (Se existir)
            if (!empty($produto->product_image)) {
                // Em vez de usar Storage::url(''), usamos parse_url do PHP para retirar o domínio
                // Exemplo: "https://meu-bucket.s3.eu-west-1.amazonaws.com/produtos/123.jpg" -> "/produtos/123.jpg"
                $caminhoRelativoAntigo = parse_url($produto->product_image, PHP_URL_PATH);
                
                // O parse_url devolve uma barra no início (ex: "/produtos/123.jpg"). O ltrim retira-a.
                $caminhoRelativoAntigo = ltrim($caminhoRelativoAntigo, '/');
                
                if (!empty($caminhoRelativoAntigo)) {
                    Storage::disk('s3')->delete($caminhoRelativoAntigo);
                }
            }
        }

        $produto->update($dados);

        return response()->json($produto);
    }

    // Se estiveres a permitir apagar produtos no teu BaseApiController, deves fazer o override do destroy
    public function destroy($id)
    {
        $produto = Product::findOrFail($id);

        // Apaga a imagem do S3 antes de apagar o produto
        if (!empty($produto->product_image)) {
            $caminhoRelativo = ltrim(parse_url($produto->product_image, PHP_URL_PATH), '/');
            if (!empty($caminhoRelativo)) {
                Storage::disk('s3')->delete($caminhoRelativo);
            }
        }

        $produto->delete();

        return response()->json(null, 204);
    }
}