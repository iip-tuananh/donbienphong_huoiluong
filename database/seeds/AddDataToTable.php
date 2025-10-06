<?php

use Illuminate\Database\Seeder;

class AddDataToTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Model\Admin\Moving::query()->truncate();
        \App\Model\Admin\Polivicy::query()->truncate();
        \App\Model\Admin\Term::query()->truncate();

        $moving = new \App\Model\Admin\Moving();
        $moving->title = 'title';
        $moving->title_eng = 'title';
        $moving->body = 'title';
        $moving->body_eng = 'title';
        $moving->created_by = 1;
        $moving->updated_by = 1;

        $moving->save();

        $p = new \App\Model\Admin\Polivicy();
        $p->created_by = 1;
        $p->updated_by = 1;
        $p->save();

        $t = new \App\Model\Admin\Term();
        $t->content = 'content';
        $t->content_eng = 'content';
        $t->created_by = 1;
        $t->updated_by = 1;

        $t->save();

    }
}
