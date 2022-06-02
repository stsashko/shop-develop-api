<?php

namespace Database\Seeders;

use App\Models\Categories;
use App\Models\Customers;
use App\Models\Deliveries;
use App\Models\Manufacturers;
use App\Models\Products;
use App\Models\Purchase_items;
use App\Models\Purchases;
use App\Models\Stores;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Products::factory(1000)->create();
//        dd($categories);
//         \App\Models\User::factory(10)->create();
    }
}
