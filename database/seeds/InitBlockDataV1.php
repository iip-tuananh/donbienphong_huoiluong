<?php

use Illuminate\Database\Seeder;

class InitBlockDataV1 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('blocks')->delete();

        \Illuminate\Support\Facades\DB::table('blocks')->insert([
            [
                'id' => 1,
                'name' => "Khối nội dung trang về chúng tôi",
                'title' => "Khối nội dung trang về chúng tôi",
                'body' => "",
                'created_by' => 1,
                'updated_by' => 1,
            ] ,
            [
                'id' => 2,
                'name' => "Khối nội dung trang liên hệ",
                'title' => "Khối nội dung trang liên hệ",
                'body' => "",
                'created_by' => 1,
                'updated_by' => 1,
            ] ,
           [
               'id' => 23,
               'name' => "Khối nội dung tầm nhìn trang chủ",
               'title' => "Khối nội dung tầm nhìn trang chủ",
               'body' => "",
               'created_by' => 1,
               'updated_by' => 1,
           ] ,
           [
               'id' => 24,
                'name' => "Khối nội dung sứ mệnh trang chủ",
                'title' => "Khối nội dung sứ mệnh trang chủ",
                'body' => "",
                'created_by' => 1,
                'updated_by' => 1,
           ]
        ]);
    }
}
