<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                "name" => "Alice",
                "email" => "alice@example.com",
                "password" => Hash::make("password123"),
            ],
            [
                "name" => "Bob",
                "email" => "bob@example.com",
                "password" => Hash::make("password123"),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
