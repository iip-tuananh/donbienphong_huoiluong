<?php

use Illuminate\Database\Seeder;

class InitBlockData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('blocks')->truncate();

        \Illuminate\Support\Facades\DB::table('blocks')->insert([
            'id' => 1,
            'name' => 'Khối ảnh cuối trang chủ',
            'title' => 'Khối ảnh cuối trang chủ',
            'body' => 'Khối ảnh cuối trang chủ',
            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }
}
