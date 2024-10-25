<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Users
        DB::table('users')->insert([
            [
                'name' => 'User 1',
                'email' => fake()->email(),
                'password' => Str::random(16)
            ],

            [
                'name' => 'User 2',
                'email' => fake()->email(),
                'password' => Str::random(16)
            ],

            [
                'name' => 'User 3',
                'email' => fake()->email(),
                'password' => Str::random(16)
            ],
        ]);



        // Wallets
        DB::table('wallets')->insert([
            [
                'user_id' => User::firstWhere('name', 'User 1')->id,
                'balance' => 10000,
            ],

            [
                'user_id' => User::firstWhere('name', 'User 2')->id,
                'balance' => 10000,
            ],

            [
                'user_id' => User::firstWhere('name', 'User 3')->id,
                'balance' => 10000,
            ],
        ]);



        // Products
        DB::table('products')->insert([
            [
                'name' => 'Phone',
                'price' => 2000,
                'quantity' => 8
            ]
        ]);

        // Orders


        // Order lines


        // Baskets
        $phoneId = Product::firstWhere('name', 'Phone')->id;
        DB::table('baskets')->insert([
            [
                'user_id' => User::firstWhere('name', 'User 1')->id,
                'product_id' => $phoneId,
                'quantity' => 5,
            ],

            [
                'user_id' => User::firstWhere('name', 'User 2')->id,
                'product_id' => $phoneId,
                'quantity' => 5,
            ],

            [
                'user_id' => User::firstWhere('name', 'User 3')->id,
                'product_id' => $phoneId,
                'quantity' => 5,
            ],
        ]);
    }
}
