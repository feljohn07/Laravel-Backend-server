<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductTableSeeder extends Seeder
{

    public function run(): void
    {

        $faker = \Faker\Factory::create();

        // And now, let's create a few articles in our database:
        for ($i = 0; $i < 50; $i++) {
            Product::create([
                'product_name' => $faker->name,
                'minimum_quantity' => 10,
                'retail_price' => 12.1,
            ]);
        }
    }
}
