<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Database\Factories\UserFactory;
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
        $user = new User();
        $user->name = 'Nelson Robles';
        $user->email = 'nelsonroblesv@gmail.com';
        $user->phone = '9811320648';
        $user->role = 'Administrador';
        $user->email_verified_at = now();
        $user->password = Hash::make('iamwao');
        $user->save();

        $user = new User();
        $user->name = 'Osberh Valle';
        $user->email = 'admin@osberthvalle.com';
        $user->phone = '9811603253';
        $user->role = 'Administrador';
        $user->email_verified_at = now();
        $user->password = Hash::make('avyna');
        $user->save();


        User::factory(6)->create();
        Customer::factory(10)->create();
    }
}
