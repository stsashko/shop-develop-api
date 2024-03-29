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

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Categories::factory(15)->create();
        Manufacturers::factory(20)->create();
        Products::factory(1500)->create();
        Stores::factory(15)->create();
        Deliveries::factory(400)->create();
        Customers::factory( 100)->create();
        Purchases::factory(350)->create();
        Purchase_items::factory(600)->create();
        User::factory(30)->create();
//        dd($categories);
//         \App\Models\User::factory(10)->create();
    }
}
