<?php

use Illuminate\Database\Seeder;

class SeedDataTemplate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $this->call(SeedDataCategory::class);
            $this->call(SeedDataOrigin::class);
            $this->call(SeedDataProduct::class);
            $this->call(SeedDataCategorySpecial::class);
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            dd($exception->getMessage());
        }


    }
}
