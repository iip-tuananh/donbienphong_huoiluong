<?php
use Illuminate\Database\Seeder;
use App\Model\G7\PaymentVoucherType;

class PaymentVoucherTypeTableSeeder extends Seeder
{
    public function run()
    {

			if(!PaymentVoucherType::find(1)) {
				PaymentVoucherType::create([
					'id' => 1,
					'name' => 'Chi nhập hàng NCC',
					'status' => 1,
					'note' => 1,
					'created_by' => 1,
					'updated_by' => 1,
				]);
			}
			if(!PaymentVoucherType::find(2)) {
				PaymentVoucherType::create([
					'id' => 2,
					'name' => 'Nhập mua tscd',
					'status' => 1,
					'note' => 1,
					'created_by' => 1,
					'updated_by' => 1,
				]);
			}
			if(!PaymentVoucherType::find(3)) {
				PaymentVoucherType::create([
					'id' => 3,
					'name' => 'Chi phí lương',
					'status' => 1,
					'note' => 1,
					'created_by' => 1,
					'updated_by' => 1,
				]);
			}
			if(!PaymentVoucherType::find(4)) {
				PaymentVoucherType::create([
					'id' => 4,
					'name' => 'Chi phí khác',
					'status' => 1,
					'note' => 1,
					'created_by' => 1,
					'updated_by' => 1,
				]);
			}
			if(!PaymentVoucherType::find(5)) {
				PaymentVoucherType::create([
					'id' => 5,
					'name' => 'Chi phí thuê địa điểm kinh doanh',
					'status' => 1,
					'note' => 1,
					'created_by' => 1,
					'updated_by' => 1,
				]);
			}
	}
}
