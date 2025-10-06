<?php

use Illuminate\Database\Seeder;

class InitDataUsersTableV2 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');
            \Illuminate\Support\Facades\DB::table('users')->truncate();
            \Illuminate\Support\Facades\DB::table('users')->insert(
                [
                    'id' => 1,
                    'name' => 'admin',
                    'email' => 'thangnguyennvt1410@gmail.com',
                    'account_name' => 'admin',
                    'password' => bcrypt('123456@'),
                    'type' => 1,
                    'status' => 1
                ]
            );

        \App\Model\Common\ModelHasRole::query()->truncate();

          foreach (  \Illuminate\Support\Facades\DB::table('users')->get() as $user) {
             \App\Model\Common\ModelHasRole::query()->insert(['model_id' => $user->id, 'model_type' => \App\Model\Common\User::class, 'role_id' => 3]);
          }

         \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
