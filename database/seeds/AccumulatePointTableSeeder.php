<?php
use Illuminate\Database\Seeder;
use App\Model\Uptek\AccumulatePoint;

class AccumulatePointTableSeeder extends Seeder
{
    public function run()
    {
		if (!AccumulatePoint::find(1)) {
			AccumulatePoint::create([
				'id' => 1,
				'value_to_point_rate' => 100000,
				'point_to_money_rate' => 1000,
				'allow_pay' => 1,
				'accumulate_pay_point' => 1,
				'created_by' => 1,
				'updated_by' => 1,
			]);
		};
	}
}
