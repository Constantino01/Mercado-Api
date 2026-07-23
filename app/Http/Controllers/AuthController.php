<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Função para Entrar
    public function login(Request $request)
    {
        // 1. Validar o pedido
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Verificar as credenciais
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'As credenciais estão incorretas.'
            ], 401);
        }

        // 3. Criar o Token do Sanctum
        // Damos o nome de 'auth_token' ao token
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4. Devolver o Token e os dados do utilizador (incluindo o cargo)
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    // Função para Sair
    public function logout(Request $request)
    {
        // Apaga o token atual que foi usado para fazer o pedido
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sessão terminada com sucesso.'
        ]);
    }

    // Função para ver quem está logado (útil para o React validar a sessão no refresh)
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}