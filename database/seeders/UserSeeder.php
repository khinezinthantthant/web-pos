<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => "asdffdsa",
            'role' => 'admin',
            'phone_number' => '098888888',
            'address' => 'yangon',
            'date_of_birth' => '1/1/2000',
            'gender' => 'female',
        ]);

    }
}
