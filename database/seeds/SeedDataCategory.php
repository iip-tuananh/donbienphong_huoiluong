<?php

use Illuminate\Database\Seeder;

class SeedDataCategory extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // tạo danh mục
        DB::table('categories')->truncate();

        DB::table('categories')->insert([
            [
                'id' => 1,
                'name' => 'Bếp Gas',
                'slug' => 'bep-ga',
                'sort_order' => 0,
                'type' => 1,
                'level' => 0,
                'parent_id' => 0,
                'created_by' => 1,
                'updated_by' => 1,
                'show_home_page' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'id' => 2,
                'name' => 'Bếp điện từ',
                'slug' => 'bep-dien-tu',
                'sort_order' => 0,
                'show_home_page' => 1,
                'type' => 1,
                'level' => 0,
                'parent_id' => 0,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'id' => 3,
                'name' => 'Lò nướng',
                'slug' => 'lo-nuong',
                'sort_order' => 0,
                'type' => 1,
                'level' => 0,
                'show_home_page' => 1,
                'parent_id' => 0,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'id' => 4,
                'name' => 'Máy hút mùi',
                'sort_order' => 0,
                'slug' => 'may-hut-mui',
                'type' => 1,
                'show_home_page' => 1,
                'level' => 0,
                'parent_id' => 0,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'id' => 5,
                'name' => 'Máy rửa chén bát',
                'slug' => 'may-rua-chen-bat',
                'sort_order' => 0,
                'type' => 1,
                'level' => 0,
                'show_home_page' => 1,
                'parent_id' => 0,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],

            // cate con
            [
                'id' => 6,
                'name' => 'Bếp ga âm',
                'slug' => 'bep-ga-am',
                'sort_order' => 1,
                'type' => 1,
                'parent_id' => 1,
                'level' => 1,
                'show_home_page' => 0,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'id' => 7,
                'name' => 'Bếp ga hồng ngoại',
                'slug' => 'bep-ga-hong-ngoai',
                'sort_order' => 1,
                'type' => 1,
                'parent_id' => 1,
                'level' => 1,
                'show_home_page' => 0,

                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'id' => 8,
                'name' => 'Bếp ga giá rẻ',
                'slug' => 'bep-ga-gia-re',
                'sort_order' => 1,
                'type' => 1,
                'show_home_page' => 0,

                'parent_id' => 1,
                'level' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],

            [
                'id' => 9,
                'name' => 'Máy hút mùi ống khói',
                'slug' => 'may-hut-mui-ong-khoi',
                'sort_order' => 1,
                'type' => 1,
                'show_home_page' => 0,

                'parent_id' => 4,
                'level' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'id' => 10,
                'name' => 'Máy hút mùi cổ điển',
                'slug' => 'may-hut-mui-co-dien',
                'sort_order' => 1,
                'type' => 1,
                'show_home_page' => 0,

                'parent_id' => 4,
                'level' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'id' => 11,
                'name' => 'Máy hút mùi âm tủ',
                'slug' => 'may-hut-mui-am-tu',
                'sort_order' => 1,
                'type' => 1,
                'parent_id' => 4,
                'show_home_page' => 0,

                'level' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
        ]);

        // tạo thương hiệu gán cho danh mục
        DB::table('manufacturers')->truncate();

        DB::table('manufacturers')->insert([
            [
                'id' => 1,
                'code' => 'taka',
                'name' => 'TAKA',
                'category_id' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'id' => 2,
                'code' => 'sunhouse',
                'name' => 'SunHouse',
                'category_id' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'id' => 3,
                'code' => 'rinnai',
                'name' => 'RinNai',
                'category_id' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],

            [
                'id' => 4,
                'code' => 'nagakawa',
                'name' => 'Nagakawa',
                'category_id' => 4,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'id' => 5,
                'code' => 'izanami',
                'name' => 'Izanami',
                'category_id' => 4,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'id' => 6,
                'code' => 'fagor',
                'name' => 'Fagor',
                'category_id' => 4,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
        ]);

    }
}
