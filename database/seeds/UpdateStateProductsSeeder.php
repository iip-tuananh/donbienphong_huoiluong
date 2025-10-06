<?php

use Illuminate\Database\Seeder;

class UpdateStateProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = \App\Model\Admin\Product::query()->get();
        foreach ($products as $product) {
            $product->state = \App\Model\Admin\Product::CON_HANG;
            $product->is_pin = \App\Model\Admin\Product::NOT_PIN;

            $product->save();
        }
    }
}
