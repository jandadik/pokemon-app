<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            [ 'email' => 'janda@janda4.cz' ],
            [
                'name' => 'Janda',
                'password' => Hash::make('hovnohovno'),
            ]
        );

        // Správné přiřazení role pomocí Spatie
        $user->assignRole('user');
    }
} 