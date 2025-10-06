<?php

namespace App\Http\Controllers\G7;

use App\Http\Controllers\Controller;
use App\Model\G7\Bill;
use App\Model\G7\G7FixedAssetImportDetail;
use App\Model\G7\PaymentVoucher;
use App\Model\G7\ReceiptVoucher;
use App\Model\G7\WarehouseExport;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use PDF;
use Response;
use Validator;
use function GuzzleHttp\Promise\all;

class BusinessReportsController extends Controller
{
    protected $view = 'g7.business_reports';
    protected $route = 'BusinessReports';

    public function index(Request $request)
    {
        $from_date = $request->get('from_date', Carbon::now()->subDays(31)->format('d/m/Y'));
        $to_date = $request->get('to_date', Carbon::now()->format('d/m/Y'));

        $from_date_carbon = Carbon::createFromFormat('d/m/Y', $from_date);
        $to_date_carbon = Carbon::createFromFormat('d/m/Y', $to_date);
        $days = $to_date_carbon->diffInDays($from_date_carbon);

        $to_date_carbon_last = Carbon::createFromFormat('d/m/Y', $from_date)->subDay();
        $to_date_last = $to_date_carbon_last->format('d/m/Y');
        $from_date_carbon_last = Carbon::createFromFormat('d/m/Y', $to_date)->subDays($days * 2 + 1);
        $from_date_last = $from_date_carbon_last->format('d/m/Y');

        list($total_receipt_in_period, $report_receipt_in_period) = ReceiptVoucher::getReportByDates($from_date_carbon->format('Y-m-d 00:00:00'),
            $to_date_carbon->format('Y-m-d 23:59:59'), [1]);

        list($total_receipt_out_period, $report_receipt_out_period) = ReceiptVoucher::getReportByDates($from_date_carbon_last->format('Y-m-d 00:00:00'),
            $to_date_carbon_last->format('Y-m-d 23:59:59'), [1]);

        $total_cost_in_period = Bill::getReportByDates($from_date_carbon->format('Y-m-d 00:00:00'),
            $to_date_carbon->format('Y-m-d 23:59:59'));

        $total_receipt_in_period += $total_cost_in_period;
        array_unshift($report_receipt_in_period, [
            'name' => 'Thu bán hàng',
            'total_value' => $total_cost_in_period
        ]);

        $total_cost_out_period = Bill::getReportByDates($from_date_carbon_last->format('Y-m-d 00:00:00'),
            $to_date_carbon_last->format('Y-m-d 23:59:59'));
        $total_receipt_out_period += $total_cost_out_period;
        array_unshift($report_receipt_out_period, [
            'name' => 'Thu bán hàng',
            'total_value' => $total_cost_out_period
        ]);

        list($total_payment_in_period, $report_payment_in_period) = PaymentVoucher::getReportByDates($from_date_carbon->format('Y-m-d 00:00:00'),
            $to_date_carbon->format('Y-m-d 23:59:59'),  [1, 2]);

        list($total_payment_out_period, $report_payment_out_period) = PaymentVoucher::getReportByDates($from_date_carbon_last->format('Y-m-d 00:00:00'),
            $to_date_carbon_last->format('Y-m-d 23:59:59'),  [1, 2]);

        // Chi giá vốn bán hàng
        $total_export_value_in_period = WarehouseExport::getReportByDates($from_date_carbon->format('Y-m-d 00:00:00'),
            $to_date_carbon->format('Y-m-d 23:59:59'));
        $total_export_value_out_period = WarehouseExport::getReportByDates($from_date_carbon_last->format('Y-m-d 00:00:00'),
            $to_date_carbon_last->format('Y-m-d 23:59:59'));

        // chi khấu hao tài sản cố định
        $report_fixed_in_period = G7FixedAssetImportDetail::getSummaryReport($from_date_carbon->format('Y-m-d 00:00:00'),
            $to_date_carbon->format('Y-m-d 23:59:59'));
        $report_fixed_out_period = G7FixedAssetImportDetail::getSummaryReport($from_date_carbon_last->format('Y-m-d 00:00:00'),
            $to_date_carbon_last->format('Y-m-d 23:59:59'));

        $total_payment_in_period += $total_export_value_in_period + $report_fixed_in_period['depreciated'];
        $total_payment_out_period += $total_export_value_out_period + $report_fixed_out_period['depreciated'];

        array_unshift($report_payment_in_period, [
            'name' => 'Nhập mua tscđ',
            'total_value' => $report_fixed_in_period['depreciated']
        ]);
        array_unshift($report_payment_in_period, [
            'name' => 'Chi giá vốn bán hàng',
            'total_value' => $total_export_value_in_period
        ]);

        array_unshift($report_payment_out_period, [
            'name' => 'Nhập mua tscđ',
            'total_value' => $report_fixed_out_period['depreciated']
        ]);
        array_unshift($report_payment_out_period, [
            'name' => 'Chi giá vốn bán hàng',
            'total_value' => $total_export_value_out_period
        ]);

        return view($this->view . '.index',
            compact('from_date', 'to_date', 'from_date_last', 'to_date_last', 'report_receipt_in_period',
                'report_receipt_out_period', 'report_payment_in_period', 'report_payment_out_period',
                'total_receipt_in_period', 'total_receipt_out_period', 'total_payment_in_period',
                'total_payment_out_period'));
    }
}
