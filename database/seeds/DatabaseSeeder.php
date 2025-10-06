<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(PermissionsTableSeeder::class);
        $this->call(PrintTemplatesTableSeeder::class);
        // $this->call(ConfigTableSeeder::class);
        // $this->call(AccumulatePointTableSeeder::class);
        // $this->call(PaymentVoucherTypeTableSeeder::class);
        // $this->call(ReceiptVoucherTypeTableSeeder::class);
    }
}
