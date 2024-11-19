<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
     protected static ?string $password;

    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Nelson Robles',
            'email' => 'nelsonroblesv@gmail.com',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('iamwao'),
            'remember_token' => Str::random(10)
        ]);
    }
}
