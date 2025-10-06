<?php
use Illuminate\Database\Seeder;
use App\Model\Uptek\Config;

class ConfigTableSeeder extends Seeder
{
    public function run()
    {
		if (!Config::find(1)) {
			Config::create([
				'id' => 1,
				'date_reminder' => 3,
			]);
		};
	}
}
