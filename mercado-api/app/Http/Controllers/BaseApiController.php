<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BaseApiController extends Controller
{
    protected $model; // Vai guardar o Modelo (ex: Product::class)
    protected $regrasValidacao = [];

    public function index()
    {
        return response()->json($this->model::all());
    }

    public function show($id)
    {
        return response()->json($this->model::findOrFail($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->regrasValidacao);
        $registo = $this->model::create($validated);

        return response()->json($registo, 201);
    }

    public function update(Request $request, $id)
    {
        $registo = $this->model::findOrFail($id);
        $validated = $request->validate($this->regrasValidacao);
        
        $registo->update($validated);
        
        return response()->json($registo);
    }

    public function destroy($id)
    {
        $registo = $this->model::findOrFail($id);
        $registo->delete();
        
        return response()->json(null, 204);
    }

    //Camião do lixo

    public function trashed() //Devolve todos os itens softdeleted
    {
        return response()->json($this->model::onlyTrashed()->get());
    }

    public function restore($id) //Restora um item softdeleted
    {
        $registo = $this->model::onlyTrashed()->findOrFail($id);
        $registo->restore();
        return response()->json($registo);
    }

    public function forceDestroy($id) //Manda um item com o caralho de vez
    {
        $registo = $this->model::onlyTrashed()->findOrFail($id);
        $registo->forceDelete();
        return response()->json(null, 204);
    }

}