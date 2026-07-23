<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends BaseApiController
{
    public function __construct()
    {
        $this->model = Category::class;
        
        $this->regrasValidacao = [
            'category_name' => 'required|string|max:255',
            'category_description' => 'nullable|string',
            'imagem' => 'nullable|image|max:5120', // Validação da imagem
            
            // Validações dos novos campos de destaque
            'is_featured' => 'nullable|boolean',
            'category_color' => ['nullable', 'string', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
        ];
    }

    public function store(Request $request)
    {
        $dados = $request->all();

        // isValid() garante que o ficheiro chegou sem estar corrompido
        if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
            $caminho = $request->file('imagem')->store('categorias', 's3');
            
            if (!$caminho) {
                return response()->json(['message' => 'Erro ao enviar a imagem para a AWS S3.'], 500);
            }

            $dados['category_image'] = Storage::disk('s3')->url($caminho);
        }

        $categoria = Category::create($dados);

        return response()->json($categoria, 201);
    }

    public function update(Request $request, $id)
    {
        $categoria = Category::findOrFail($id);
        $dados = $request->all();

        if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
            
            // 1. Guarda a NOVA imagem
            $caminhoNovo = $request->file('imagem')->store('categorias', 's3');

            if (!$caminhoNovo) {
                return response()->json(['message' => 'Erro ao enviar a imagem para a AWS S3.'], 500);
            }

            // Atualiza os dados com a NOVA URL
            $dados['category_image'] = Storage::disk('s3')->url($caminhoNovo);

            // 2. Apaga a imagem ANTIGA do S3 extraindo o caminho com parse_url (Evita o erro Key Length = 0)
            if (!empty($categoria->category_image)) {
                $caminhoRelativoAntigo = ltrim(parse_url($categoria->category_image, PHP_URL_PATH), '/');
                
                if (!empty($caminhoRelativoAntigo)) {
                    Storage::disk('s3')->delete($caminhoRelativoAntigo);
                }
            }
        }

        $categoria->update($dados);

        return response()->json($categoria);
    }

    // Sobrescrever o destroy para limpar a AWS
    public function destroy($id)
    {
        $categoria = Category::findOrFail($id);

        // Apaga a imagem do S3 antes de apagar a categoria
        if (!empty($categoria->category_image)) {
            $caminhoRelativo = ltrim(parse_url($categoria->category_image, PHP_URL_PATH), '/');
            
            if (!empty($caminhoRelativo)) {
                Storage::disk('s3')->delete($caminhoRelativo);
            }
        }

        $categoria->delete();

        return response()->json(null, 204);
    }
}