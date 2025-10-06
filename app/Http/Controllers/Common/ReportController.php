<?php

namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use App\Model\Common\PromoCampaign;
use App\Model\Common\User;
use App\Model\Common\Customer;
use App\Model\Uptek\G7Info;
use App\Model\Uptek\PrintTemplate;
use App\Model\G7\Bill;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;
use Validator;
use App\Employee;
use Auth;
use \stdClass;
use Response;
use Rap2hpoutre\FastExcel\FastExcel;
use PDF;
use App\Http\Controllers\Controller;
use \Carbon\Carbon;
use DB;
use App\Http\Traits\ResponseTrait;
use \Vanthao03596\HCVN\Models\Province;

class ReportController extends Controller
{

	protected $view = 'common.reports';
	protected $route = 'Report';

	public function promoReport()
	{
		return view($this->view.'.promoReport');
	}

    public function promoReportSearchData(Request $request)
    {
		return successResponse(
			"",
			Bill::promoReportSearchData($request)->paginate($request->per_page, '', 'page', $request->current_page),
			['summary' => Bill::promoReportSummary($request), 'draw' => $request->draw]
		);
    }

	public function promoReportPrint(Request $request)
	{
		$template = PrintTemplate::findOrFail(PrintTemplate::BAO_CAO_KHUYEN_MAI);
		$template = fillReport($template->template, $this->promoReportPrintData($request));
        $template = clearNull($template);
		return view('print', compact(['template']));
	}

	public function promoReportPrintData($request)
	{
		$result = [];
		$result['CTKM'] = $request->promo_id ? PromoCampaign::find($request->promo_id)->name : '';
		$result['TU_NGAY'] = $request->from_date ? Carbon::parse($request->from_date)->format('d/m/Y') : '';
		$result['DEN_NGAY'] = $request->to_date ? Carbon::parse($request->to_date)->format('d/m/Y') : '';
		$result['KHACH_HANG'] = $request->customer_id ? Customer::find($request->customer_id)->name : '';
		$result['GARA'] = $request->g7_id ? G7Info::find($request->g7_id)->name : '';

		$data = Bill::promoReportSearchData($request)->get();
		$summary = Bill::promoReportSummary($request);

		$body = '';

		foreach($data as $index => $d) {
            $body .= '<tr>';
            $body .= '<td style="text-align: center;">'.($index + 1).'</td>';
            $body .= '<td>'.$d->code.'</td>';
			$body .= '<td>'.Carbon::parse($d->bill_date)->format('d/m/Y H:i').'</td>';
            $body .= '<td style="text-align: right;">'.formatCurrency($d->cost_after_sale).'</td>';
            $body .= '<td style="text-align: right;">'.formatCurrency($d->promo_value).'</td>';
			$body .= '<td>'.$d->customer_name.'</td>';
			$body .= '<td>'.$d->license_plate.'</td>';
			$body .= '<td>'.$d->g7.'</td>';
            $body .= '</tr>';
        }

		$body .= '<tr>';
		$body .= '<td colspan="3" style="text-align: center;"><b>Tổng cộng</b></td>';
		$body .= '<td style="text-align: right;"><b>'.formatCurrency($summary->cost_after_sale).'</b></td>';
		$body .= '<td style="text-align: right;"><b>'.formatCurrency($summary->promo_value).'</b></td>';
		$body .= '<td colspan="3"></td>';
		$body .= '</tr>';

        $table = '<table style="width: 100%;">
            <thead>
                <tr>
                <td style="text-align: center"><b>STT</b></td>
                <td style="text-align: center" ><b>Số hóa đơn</b></td>
                <td style="text-align: center"><b>Ngày hóa đơn</b></td>
                <td style="text-align: center"><b>Giá trị hóa đơn</b></td>
                <td style="text-align: center"><b>Giá trị KM</b></td>
                <td style="text-align: center"><b>Khách hàng</b></td>
				<td style="text-align: center"><b>Xe</b></td>
				<td style="text-align: center"><b>Gara</b></td>
                </tr>
            </thead>
            <tbody>'
            .$body.
            '</tbody>
        </table>';

		$result['CHI_TIET'] = $table;
		return $result;
	}

	public function promoProductReport()
	{
		return view($this->view.'.promoProductReport');
	}

    public function promoProductReportSearchData(Request $request)
    {
		return successResponse(
			"",
			Bill::promoProductReportSearchData($request)->paginate($request->per_page, '', 'page', $request->current_page),
			['summary' => Bill::promoProductReportSummary($request), 'draw' => $request->draw]
		);
    }

	public function promoProductReportPrint(Request $request)
	{
		$template = PrintTemplate::findOrFail(PrintTemplate::BAO_CAO_KHUYEN_MAI_HANG_HOA);
		$template = fillReport($template->template, $this->promoProductReportPrintData($request));
        $template = clearNull($template);
		return view('print', compact(['template']));
	}

	public function promoProductReportPrintData($request)
	{
		$result = [];
		$result['CTKM'] = $request->promo_id ? PromoCampaign::find($request->promo_id)->name : '';
		$result['TU_NGAY'] = $request->from_date ? Carbon::parse($request->from_date)->format('d/m/Y') : '';
		$result['DEN_NGAY'] = $request->to_date ? Carbon::parse($request->to_date)->format('d/m/Y') : '';
		$result['KHACH_HANG'] = $request->customer_id ? Customer::find($request->customer_id)->name : '';
		$result['GARA'] = $request->g7_id ? G7Info::find($request->g7_id)->name : '';

		$data = Bill::promoProductReportSearchData($request)->get();
		$summary = Bill::promoProductReportSummary($request);

		$body = '';

		foreach($data as $id => $d) {
			$rowspan = count($d->promo_products) ?: 1;
			$body .= '<tr>';
			$body .= '<td rowspan="'.$rowspan.'" style="text-align: center;">'.($id + 1).'</td>';
			$body .= '<td rowspan="'.$rowspan.'">'.$d->code.'</td>';
			$body .= '<td rowspan="'.$rowspan.'">'.Carbon::parse($d->bill_date)->format('d/m/Y H:i').'</td>';
			$body .= '<td rowspan="'.$rowspan.'" style="text-align: right;">'.formatCurrency($d->cost_after_sale).'</td>';
			if (count($d->promo_products)) {
				$body .= '<td>'.$d->promo_products[0]->product_name.'</td>';
				$body .= '<td>'.$d->promo_products[0]->code.'</td>';
				$body .= '<td style="text-align: center">'.formatCurrency($d->promo_products[0]->qty).'</td>';
			} else {
				$body .= '<td colspan="3">Không có hàng khuyến mại</td>';
			}
			$body .= '<td rowspan="'.$rowspan.'">'.$d->customer_name.'</td>';
			$body .= '<td rowspan="'.$rowspan.'">'.$d->license_plate.'</td>';
			$body .= '<td rowspan="'.$rowspan.'">'.$d->g7.'</td>';
			$body .= '</tr>';
			for ($i = 1; $i < count($d->promo_products); $i++) {
				$p = $d->promo_products[$i];
				$body .= '<td>'.$p->product_name.'</td>';
				$body .= '<td>'.$p->code.'</td>';
				$body .= '<td style="text-align: center">'.formatCurrency($p->qty).'</td>';
			}
        }

		$body .= '<tr>';
		$body .= '<td colspan="6" style="text-align: center;"><b>Tổng cộng</b></td>';
		$body .= '<td style="text-align: center;"><b>'.formatCurrency($summary->qty).'</b></td>';
		$body .= '<td colspan="3"></td>';

        $table = '<table style="width: 100%;">
            <thead>
                <tr>
					<td rowspan="2" style="text-align: center"><b>STT</b></td>
					<td rowspan="2" style="text-align: center"><b>Số hóa đơn</b></td>
					<td rowspan="2" style="text-align: center"><b>Ngày hóa đơn</b></td>
					<td rowspan="2" style="text-align: center"><b>Giá trị hóa đơn</b></td>
					<td colspan="3" style="text-align: center"><b>Khuyến mại</b></td>
					<td rowspan="2" style="text-align: center"><b>Khách hàng</b></td>
					<td rowspan="2" style="text-align: center"><b>Xe</b></td>
					<td rowspan="2" style="text-align: center"><b>Gara</b></td>
                </tr>
				<tr>
					<td>Tên hàng</td>
					<td>Mã hàng</td>
					<td>SL</td>
				</tr>
            </thead>
            <tbody>'
            .$body.
            '</tbody>
        </table>';

		$result['CHI_TIET'] = $table;
		return $result;
	}

	public function customerSaleReport()
	{
		return view($this->view.'.customerSaleReport');
	}

    public function customerSaleReportSearchData(Request $request)
    {
		return successResponse(
			"",
			Bill::customerSaleReportSearchData($request)->paginate($request->per_page, '', 'page', $request->current_page),
			['summary' => Bill::customerSaleReportSummary($request), 'draw' => $request->draw]
		);
    }

	public function customerSaleReportPrint(Request $request)
	{
		$template = PrintTemplate::findOrFail(PrintTemplate::BAO_CAO_BAN_HANG_THEO_KHACH);
		$template = fillReport($template->template, $this->customerSaleReportPrintData($request));
        $template = clearNull($template);
		return view('print', compact(['template']));
	}

	public function customerSaleReportPrintData($request)
	{
		$result = [];
		$result['TU_NGAY'] = $request->from_date ? Carbon::parse($request->from_date)->format('d/m/Y') : '';
		$result['DEN_NGAY'] = $request->to_date ? Carbon::parse($request->to_date)->format('d/m/Y') : '';
		$result['TINH'] = $request->province_id ? Province::find($request->province_id)->name : '';
		$result['KHACH_HANG'] = $request->customer_id ? Customer::find($request->customer_id)->name : '';
		$result['GARA'] = $request->g7_id ? G7Info::find($request->g7_id)->name : '';

		$data = Bill::customerSaleReportSearchData($request)->get();
		$summary = Bill::customerSaleReportSummary($request);

		$body = '';

		foreach($data as $index => $d) {
            $body .= '<tr>';
            $body .= '<td style="text-align: center;">'.($index + 1).'</td>';
            $body .= '<td>'.$d->code.' - '.$d->name.'</td>';
			$body .= '<td>'.$d->mobile.'</td>';
			$body .= '<td>'.$this->getFullAdress($d).'</td>';
            $body .= '<td style="text-align: right;">'.formatCurrency($d->total_cost).'</td>';
            $body .= '<td style="text-align: right;">'.formatCurrency($d->payed).'</td>';
			$body .= '<td style="text-align: right;">'.formatCurrency($d->remain).'</td>';
            $body .= '</tr>';
        }

		$body .= '<tr>';
		$body .= '<td colspan="4" style="text-align: center;"><b>Tổng cộng</b></td>';
		$body .= '<td style="text-align: right;"><b>'.formatCurrency($summary->total_cost).'</b></td>';
		$body .= '<td style="text-align: right;"><b>'.formatCurrency($summary->payed).'</b></td>';
		$body .= '<td style="text-align: right;"><b>'.formatCurrency($summary->remain).'</b></td>';
		$body .= '</tr>';

        $table = '<table style="width: 100%;">
            <thead>
                <tr>
                <td style="text-align: center"><b>STT</b></td>
                <td style="text-align: center"><b>Khách hàng</b></td>
                <td style="text-align: center"><b>Số điện thoại</b></td>
                <td style="text-align: center"><b>Địa chỉ</b></td>
                <td style="text-align: center"><b>Tổng giá trị mua</b></td>
                <td style="text-align: center"><b>Giá trị đã thanh toán</b></td>
				<td style="text-align: center"><b>Công nợ còn lại</b></td>
                </tr>
            </thead>
            <tbody>'
            .$body.
            '</tbody>
        </table>';

		$result['CHI_TIET'] = $table;
		return $result;
	}

	private function getFullAdress($customer)
    {
        return ($customer->address ? $customer->address.", " : "").
            ($customer->ward ? $customer->ward.", " : "").
            ($customer->district ? $customer->district.", " : "").
            ($customer->province ? $customer->province : "");
    }
}
