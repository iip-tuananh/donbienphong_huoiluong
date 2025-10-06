<?php

namespace App\Http\Controllers\Common;

use App\Model\Admin\Contact;
use App\Model\Admin\Project;
use App\Model\Admin\Service;
use App\Model\Common\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Validator;
use \stdClass;
use Response;
use Rap2hpoutre\FastExcel\FastExcel;
use PDF;
use App\Http\Controllers\Controller;
use \Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Auth;
use App\Model\Admin\Config;
use App\Model\Admin\Product;
use App\Model\Admin\Post;
use App\Model\Common\User;

use Spatie\Analytics\Period;
use Analytics;
use App\Libraries\GoogleAnalytics;
use App\Model\Admin\OrderDetail;
use App\Model\Admin\Order;

class DashboardController extends Controller
{
	protected $view = 'admin.dashboard';

	public function index()
	{
		$data = [];
		$g7_ids = [];
		$data['orders'] = Order::whereDate('created_at',Carbon::now())->count();
		$data['total_price'] = OrderDetail::whereDate('created_at',Carbon::today())
								->sum('price');
        $data_analytics = [];

        $data['blogs'] = Post::query()->get()->count();
        $data['customers'] = Customer::query()->get()->count();
        $data['contacts'] = Contact::query()->get()->count();

        $visitsByWeek = DB::table('visits')
            ->selectRaw("YEARWEEK(visited_at, 1) as yw, COUNT(*) as visits")
            ->groupBy('yw')
            ->orderBy('yw')
            ->get()
            ->map(function($item) {
                $year = substr($item->yw, 0, 4);
                $week = substr($item->yw, 4, 2);
                $date = Carbon::now()
                    ->setISODate($year, $week)
                    ->startOfWeek();
                return [
                    'label'  => $date->locale('vi')->isoFormat('DD MMM'),
                    'visits' => $item->visits,
                ];
            });

        $visitsByMonth = DB::table('visits')
            ->selectRaw("DATE_FORMAT(visited_at, '%Y-%m') as ym, COUNT(*) as visits")
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->map(function($item) {
                return [
                    'label'  => Carbon::createFromFormat('Y-m', $item->ym)
                        ->locale('vi')
                        ->isoFormat('MMMM YYYY'),
                    'visits' => $item->visits,
                ];
            });

		$sales = $this->getOrderByDay();

		return view($this->view.'.dash', compact('data','data_analytics','sales', 'visitsByMonth', 'visitsByWeek'));
	}

	public function getOrderByDay()
		{
			$select = [
				DB::raw("SUM(price) as total_price"),
				DB::raw("DATE(created_at) as day"),
			];
			$result = OrderDetail::select($select)->whereDate('created_at', '>',
					Carbon::now()->subDays(10))->groupBy([DB::raw('DATE(created_at)')])->get();

			return $result;
		}
}
