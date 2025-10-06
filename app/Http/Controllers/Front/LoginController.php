<?php

namespace App\Http\Controllers\Front;

use App\Http\Traits\ResponseTrait;
use App\Mail\NewOrder;
use App\Model\Admin\Config;
use App\Model\Admin\Order;
use App\Model\Admin\OrderDetail;
use App\Model\Admin\Product;
use App\Model\Common\Customer;
use App\Model\Common\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Admin\Category;
use App\Model\Admin\Voucher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Kjmtrue\VietnamZone\Models\Province;
use Tymon\JWTAuth\Facades\JWTAuth;
use Vanthao03596\HCVN\Models\District;
use Vanthao03596\HCVN\Models\Province as Vanthao03596Province;
use Vanthao03596\HCVN\Models\Ward;

class LoginController extends Controller
{
    use ResponseTrait;


    public function showLoginForm()
    {
        return view('site.customers.login');
    }

    public function login(Request $request)
    {
        $rules = [
            'email'     => 'required|string|max:255',
            'password'     => 'required|string',
        ];

        $messages = [
            'email.required'        => 'Vui lòng nhập email',
            'password.required'     => 'Vui lòng nhập mật khẩu',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return $this->responseErrors('Đăng nhập thất bại!', $validator->errors());
        }

        $remember = true;

        if (! auth('customer')->attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
            return $this->responseErrors('Đăng nhập thất bại. Kiểm tra lại tên đăng nhập hoặc mật khẩu!');
        }

        $customer = Auth::guard('customer')->user();

        if ($customer->status != 1) {
            Auth::guard('customer')->logout();
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản của bạn đang bị khóa.',
            ]);
        }

        $token = Auth::guard('customer')->attempt(['email' => $request->email, 'password' => $request->password, 'status' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công!',
            'redirect_url' => route('front.home-page'),
            'token'        => $token,
        ]);

    }

    public function logout(Request $request) {
        Auth::guard('customer')->logout();
    }


}
