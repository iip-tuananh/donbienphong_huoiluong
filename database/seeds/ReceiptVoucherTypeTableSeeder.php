<?php
use Illuminate\Database\Seeder;
use App\Model\G7\ReceiptVoucherType;

class ReceiptVoucherTypeTableSeeder extends Seeder
{
    public function run()
    {
		if(!DB::table('receipt_voucher_types')->count()) {
			ReceiptVoucherType::create([
				'id' => 1,
				'name' => 'Thu bán hàng',
				'status' => 1,
				'note' => 1,
				'created_by' => 1,
				'updated_by' => 1,
			]);
			ReceiptVoucherType::create([
				'id' => 2,
				'name' => 'Thu Nhà cung cấp',
				'status' => 1,
				'note' => 1,
				'created_by' => 1,
				'updated_by' => 1,
			]);
			ReceiptVoucherType::create([
				'id' => 3,
				'name' => 'Thu Khác',
				'status' => 1,
				'note' => 1,
				'created_by' => 1,
				'updated_by' => 1,
			]);
		}
	}
}
