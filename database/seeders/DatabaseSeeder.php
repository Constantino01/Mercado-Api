<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Verifica se o admin já existe para evitar duplicados se correres o comando duas vezes
        if (!User::where('email', 'admin@orestes.pt')->exists()) {
            User::create([
                'name' => 'Administrador',
                'email' => 'admin@orestes.pt',
                'password' => Hash::make('MudarParaUmaPasswordForte123!'), // Define a tua password aqui
                'role' => 'admin',
            ]);
            
            $this->command->info('Conta de Administrador criada com sucesso!');
        } else {
            $this->command->info('O Administrador já existe na base de dados.');
        }
    }
}