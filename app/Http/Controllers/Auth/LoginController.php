<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Validator;
use Tymon\JWTAuth\Facades\JWTFactory;
use JWTAuth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = 'admin/common/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        if (!session()->has('url.intended'))
        {
            session(['url.intended' => url()->previous()]);
        }
        return view('auth.login');
    }

    public function login (Request $request)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'account_name' => 'required',
                'password' => 'required',
            ]
        );

        if ($validate->fails()) {
            $message = array(
                "message" => "Thông tin đăng nhập chưa đủ!",
                "alert-type" => "warning"
            );
            return back()
                ->withErrors($validate)
                ->with($message)
                ->withInput();
        }

        $remember = true;
        // if(isset($request->remember_me)) {
        //     $remember = true;
        // }

        if (Auth::attempt(['account_name' => $request->account_name, 'password' => $request->password, 'status' => 1], $remember)) {
            // Đăng nhập thành công
            $token = JWTAuth::attempt(['account_name' => $request->account_name, 'password' => $request->password, 'status' => 1]);

            $message = array(
                "message" => "Đăng nhập thành công!",
                "alert-type" => "success",
                "token" => $token
            );
            return redirect()->route('index')->with($message);
        } else {
            // Đăng nhập thất bại, trả về thông báo lỗi.
            $message = array(
                "message" => "Đăng nhập thất bại, vui lòng thử lại!",
                "alert-type" => "warning"
            );
            return redirect()->route('login')->with($message);
        }
    }

    public function logout() {
        Auth::logout();
        $message = array(
            "logout" => "logout"
        );
        return redirect()->route('login')->with($message);
    }
}
