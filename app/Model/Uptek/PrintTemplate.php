<?php

namespace App\Model\Uptek;
use Auth;
use App\Model\BaseModel;
use App\Model\Common\User;
use Illuminate\Database\Eloquent\Model;
use stdClass;

class PrintTemplate extends BaseModel
{
    protected $table = 'print_templates';

    public CONST PHIEU_THU_TIEN = 1;
    public CONST HOA_DON_BAN = 2;
	public CONST HOA_DON_BAN_57 = 3;
	public CONST BAO_CAO_KHUYEN_MAI = 4;
	public CONST BAO_CAO_KHUYEN_MAI_HANG_HOA = 5;
	public CONST PHIEU_NHAP_KHO = 6;
    public CONST PHIEU_NHAP_TSCD= 7;
    public CONST PHIEU_XUAT_KHO= 8;
	public CONST BAO_CAO_BAN_HANG_THEO_KHACH = 9;

    public static function getReceiptVoucherTemplate() {
        return self::PHIEU_THU_TIEN;
    }

    public static function getBillTemplate() {
        return self::HOA_DON_BAN;
    }

    public static function getWarehouseImportTemplate() {
        return self::PHIEU_NHAP_KHO;
    }


    public function canDelete() {
        return false;
    }

    public static function searchByFilter($request) {
        $result = self::query();

        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%'.$request->name.'%');
        }

        if (!empty($request->code)) {
            $result = $result->where('code', 'like', '%'.$request->code.'%');
        }

        if (!empty($request->service_type)) {
            $result = $result->where('service_type_id', $request->service_type);
        }

        if ($request->status === 0 || $request->status === '0' || !empty($request->status)) {
            $result = $result->where('status', $request->status);
        }

        $result = $result->orderBy('created_at','desc')->get();
        return $result;
    }

    public static function getForSelect() {
        return self::where('status', 1)
            ->select(['id', 'name'])
            ->orderBy('name', 'ASC')
            ->get();
    }

    public static function getDataForEdit($id) {
        return self::where('id', $id)
            ->with([])
            ->firstOrFail();
    }

    public function generateCode() {
        $this->code = "MAUIN-".generateCode(4, $this->id);
        $this->save();
    }
}
