<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // 🛡️ O nosso "Segurança" interno
    private function checkAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Apenas o Administrador pode gerir contas.');
        }
    }

    // Listar todos
    public function index()
    {
        $this->checkAdmin(); // Bloqueia se não for admin
        return response()->json(User::all());
    }

    // Criar (POST)
    public function store(Request $request)
    {
        $this->checkAdmin(); // Bloqueia se não for admin

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,funcionario',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return response()->json($user, 201);
    }

    // Mostrar um específico
    public function show(User $utilizadore) 
    {
        $this->checkAdmin(); // Bloqueia se não for admin
        return response()->json($utilizadore);
    }

    // Atualizar (PUT)
    public function update(Request $request, User $utilizadore)
    {
        $this->checkAdmin(); // Bloqueia se não for admin

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|unique:users,email,' . $utilizadore->id,
            'password' => 'nullable|string|min:8',
            'role' => 'sometimes|in:admin,funcionario',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $utilizadore->update($validated);
        return response()->json($utilizadore);
    }

    // Eliminar
    public function destroy(User $utilizadore)
    {
        $this->checkAdmin(); // Bloqueia se não for admin
        
        // Medida extra de segurança pragmática: Não deixamos o Admin apagar-se a si próprio
        if (auth()->id() === $utilizadore->id) {
            return response()->json(['message' => 'Não te podes apagar a ti próprio!'], 400);
        }

        $utilizadore->delete();
        return response()->json(null, 204);
    }
}