<?php
namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    // Retorna as configurações num formato simples: {"allow_purchases": "1"}
    public function index()
    {
        $settings = Setting::pluck('value', 'key'); 
        return response()->json($settings);
    }

    // Atualiza ou cria uma configuração
    public function update(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required|string',
        ]);

        $setting = Setting::updateOrCreate(
            ['key' => $request->key],
            ['value' => $request->value]
        );

        return response()->json($setting);
    }
}