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
                "name" => "Sam",
                "email" => "samuellalley@gmail.com",
                "password" => Hash::make("straynge2"),
            ],
            [
                "name" => "Will",
                "email" => "will@example.com",
                "password" => Hash::make("password"),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
