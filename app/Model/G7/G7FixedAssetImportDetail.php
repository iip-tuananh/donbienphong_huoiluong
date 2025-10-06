<?php

namespace App\Model\G7;

use Illuminate\Database\Eloquent\Model;
use Auth;

class G7FixedAssetImportDetail extends Model
{

    protected $table = "g7_fixed_asset_import_details";

    public function asset()
    {
        return $this->belongsTo(G7FixedAsset::class, 'asset_id', 'id');
    }

    public static function searchReportByFilter($request) {
        $result = self::query()
            ->with('asset')
            ->select([
                'qty',
                'price',
                'total_price',
                'asset_id',
                'g7_fixed_asset_imports.code as import_code',
                'g7_fixed_asset_imports.import_date'
            ])
            ->join('g7_fixed_asset_imports', 'g7_fixed_asset_imports.id', '=', 'g7_fixed_asset_import_details.parent_id')
            ->where([
                'g7_fixed_asset_imports.g7_id' => Auth::user()->g7_id,
                'g7_fixed_asset_imports.status' => 1,
            ]);

        if (!empty($request->name)) {
            $result = $result->where('g7_fixed_asset_import_details.name', 'like', '%'.$request->name.'%');
        }

        if (!empty($request->code)) {
            $result = $result->where('g7_fixed_asset_imports.code', 'like', '%'.$request->code.'%');
        }

        return $result->orderBy('g7_fixed_asset_imports.import_date','desc')->get();
    }

    /**
     * @param null $from_date
     * @param null $to_date
     * @return int[]
     */
    public static function getSummaryReport($from_date = null, $to_date = null) {
        $rows = self::query()
            ->with('asset')
            ->select([
                'qty',
                'price',
                'total_price',
                'asset_id',
                'g7_fixed_asset_imports.import_date'
            ])
            ->join('g7_fixed_asset_imports', 'g7_fixed_asset_imports.id', '=', 'g7_fixed_asset_import_details.parent_id')
            ->where([
                'g7_fixed_asset_imports.g7_id' => Auth::user()->g7_id,
                'g7_fixed_asset_imports.status' => 1,
            ])->get();
        $report = [
            'qty' => 0,
            'total_price' => 0,
            'depreciated' => 0
        ];
        foreach ($rows as $row) {
            $report['qty'] += $row->qty;
            $report['total_price'] += $row->total_price;
            $report['depreciated'] += G7FixedAsset::getDepreciated($row->price, $row->asset->depreciation_period, $row->import_date, $row->qty, $from_date, $to_date);
        }
        return $report;
    }
}
