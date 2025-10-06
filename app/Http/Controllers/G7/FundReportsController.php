<?php

namespace App\Http\Controllers\G7;

use App\Model\G7\PaymentVoucher;
use App\Model\G7\ReceiptVoucher;
use Illuminate\Http\Request;
use App\Model\G7\FundAccount as ThisModel;
use Yajra\DataTables\DataTables;
use Validator;
use \stdClass;
use Response;
use Rap2hpoutre\FastExcel\FastExcel;
use PDF;
use App\Http\Controllers\Controller;
use \Carbon\Carbon;
use DB;

class FundReportsController extends Controller
{
    protected $view = 'g7.fund_reports';
    protected $route = 'FundReports';

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
            $to_date_carbon->format('Y-m-d 23:59:59'));

        list($total_receipt_out_period, $report_receipt_out_period) = ReceiptVoucher::getReportByDates($from_date_carbon_last->format('Y-m-d 00:00:00'),
            $to_date_carbon_last->format('Y-m-d 23:59:59'));

        list($total_payment_in_period, $report_payment_in_period) = PaymentVoucher::getReportByDates($from_date_carbon->format('Y-m-d 00:00:00'),
            $to_date_carbon->format('Y-m-d 23:59:59'));

        list($total_payment_out_period, $report_payment_out_period) = PaymentVoucher::getReportByDates($from_date_carbon_last->format('Y-m-d 00:00:00'),
            $to_date_carbon_last->format('Y-m-d 23:59:59'));

        return view($this->view . '.index',
            compact('from_date', 'to_date', 'from_date_last', 'to_date_last', 'report_receipt_in_period',
                'report_receipt_out_period', 'report_payment_in_period', 'report_payment_out_period',
            'total_receipt_in_period', 'total_receipt_out_period', 'total_payment_in_period', 'total_payment_out_period'));
    }
}
