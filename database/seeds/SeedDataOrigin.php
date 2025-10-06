<?php

use Illuminate\Database\Seeder;

class SeedDataOrigin extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('origins')->truncate();
        DB::table('origins')->insert([
            [
                'id' => 1,
                'code' => 'japan',
                'name' => 'Nhật Bản',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'id' => 2,
                'code' => 'usa',
                'name' => 'Mỹ',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'id' => 3,
                'code' => 'vn',
                'name' => 'Việt Nam',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'id' => 4,
                'code' => 'cn',
                'name' => 'Trung Quốc',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]
        ]);
    }
}
