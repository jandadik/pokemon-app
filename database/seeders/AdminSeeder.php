<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // Import User modelu
use Illuminate\Support\Facades\Hash; // Import Hash fasády

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vytvoření administrátorského uživatele
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'], // Hledáme podle emailu, abychom neduplikovali
            [
                'name' => 'Admin',
                'password' => Hash::make('password'), // Heslo bude "password"
                'email_verified_at' => now(), // Označíme email jako ověřený
            ]
        );

        // Přiřazení role 'admin'
        // Předpokládá se, že používáš balíček Spatie Laravel Permission a role 'admin' existuje
        $admin->assignRole('admin'); 
    }
}
