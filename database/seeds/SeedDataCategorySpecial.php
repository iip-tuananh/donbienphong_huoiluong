<?php

use Illuminate\Database\Seeder;

class SeedDataCategorySpecial extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // tạo danh mục
        DB::table('category_special')->truncate();
        DB::table('post_categories')->truncate();
        DB::table('post_category_special')->truncate();
        DB::table('posts')->truncate();

        // danh mục đặc biệt, type 10 - sản phẩm, type 20 bài viết
        DB::table('category_special')->insert(
            [
                [
                    'code' => 'san-pham-ban-chay',
                    'slug' => 'san-pham-ban-chay',
                    'name' => 'Sản phẩm bán chạy',
                    'created_by' => '1',
                    'updated_by' => '1',
                    'show_home_page' => 1,
                    'type' => 10,
                    'order_number' => 1,
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now(),
                ],
                [
                    'code' => 'tin-doc-nhieu',
                    'slug' => 'tin-doc-nhieu',
                    'name' => 'Tin đọc nhiều',
                    'created_by' => '1',
                    'updated_by' => '1',
                    'show_home_page' => 1,
                    'type' => 20,
                    'order_number' => 1,
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now(),
                ],
            ]
        );

        // danh mục của bài viết
        DB::table('post_categories')->insert(
            [
                [
                    'slug' => 'goc-tu-van',
                    'name' => 'Góc tư vấn',
                    'created_by' => '1',
                    'updated_by' => '1',
                    'sort_order' => 0,
                    'parent_id' => 0,
                    'level' => 0,
                    'show_home_page' => 1,
                    'type' => 1,
                    'order_number' => 1,
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now(),
                ],
                [
                    'slug' => 'tin-dich-vu',
                    'name' => 'Tin dịch vụ',
                    'created_by' => '1',
                    'updated_by' => '1',
                    'sort_order' => 0,
                    'parent_id' => 0,
                    'level' => 0,
                    'show_home_page' => 1,
                    'type' => 1,
                    'order_number' => 2,
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now(),
                ],
            ]
        );
    }
}
