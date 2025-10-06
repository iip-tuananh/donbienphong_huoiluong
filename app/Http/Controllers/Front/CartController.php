<?php

namespace App\Http\Controllers\Front;

use App\Mail\NewOrder;
use App\Model\Admin\Config;
use App\Model\Admin\Order;
use App\Model\Admin\OrderDetail;
use App\Model\Admin\Post;
use App\Model\Admin\Product;
use App\Model\Common\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Admin\Category;
use App\Model\Admin\Voucher;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Kjmtrue\VietnamZone\Models\Province;
use Vanthao03596\HCVN\Models\District;
use Vanthao03596\HCVN\Models\Province as Vanthao03596Province;
use Vanthao03596\HCVN\Models\Ward;

class CartController extends Controller
{
    // trang giỏ hàng
    public function index()
    {
        $cart = \Cart::session('cartList');
        $cartCollection = $cart->getContent();
        $total_price = $cart->getTotal();

        $items = $cart->getContent()->values();
        $total_qty = $items->sum('quantity');

        $productsRandom = Product::where('status', 1)
            ->inRandomOrder()
            ->limit(5)
            ->get();

        return view('site.orders.cart', compact('cartCollection', 'total_price', 'total_qty', 'productsRandom'));
    }

    public function addItem(Request $request, $postId)
    {
        $post = Post::query()->find($postId);
        $cartList  = \Cart::session('cartList');

        if ($cartList->get($post->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Bài viết này đã được thêm vào giỏ hàng',
                'items' => $cartList->getContent(),
                'count' => $cartList->getContent()->count(),
            ]);
        }

        $cartList->add([
            'id' => $post->id,
            'name' => $post->name,
            'price' => $post->price ?? 0,
            'quantity' => $post->qty ? (int)$request->qty : 1,
            'attributes' => [
                'image' => $post->image->path ?? '',
                'slug' => $post->slug,
            ]
        ]);

        return \Response::json(['success' => true, 'items' => $cartList->getContent(), 'total' => $cartList->getTotal(),
            'count' => $cartList->getContent()->sum('quantity')]);
    }

    public function updateItem(Request $request)
    {
        $cartList  = \Cart::session('cartList');

        $cartList->update($request->product_id, array(
            'quantity' => array(
                'relative' => false,
                'value' => $request->qty
            ),
        ));

        return \Response::json(['success' => true, 'items' => \Cart::getContent(), 'total' => \Cart::getTotal(),
            'count' => \Cart::getContent()->sum('quantity')]);

    }

    public function removeItem(Request $request)
    {
        $cartList = \Cart::session('cartList');

        $cartList->remove($request->post_id);

        return \Response::json(['success' => true, 'items' => $cartList->getContent(), 'total' => $cartList->getTotal(),
            'count' => $cartList->getContent()->sum('quantity')]);
    }

    // trang thanh toán
    public function checkout(Request $request) {

        $cart = \Cart::session('cartList');
        $cartCollection = $cart->getContent();
        $total = $cart->getTotal();

        if($cartCollection->isEmpty()) return redirect()->route('front.home-page');

        // sinh mã đơn hàng
        $ms = (int) round(microtime(true) * 1000);
        $customerId = auth('customer')->id();
        $postId = $cartCollection->pluck('id')->toArray()[0];
        $cid  = strtoupper(base_convert((string)$customerId, 10, 36)); // VD: 15 -> F
        $pid  = strtoupper(base_convert((string)$postId,     10, 36)); // VD: 123 -> 3F
        $time = strtoupper(base_convert((string)$ms,         10, 36)); // VD: L4N9OA
        $rand = strtoupper(str_pad(base_convert((string) random_int(0, 36**3 - 1), 10, 36), 3, '0', STR_PAD_LEFT)); // 3 ký tự

        $orderCode = 'DH-' . $cid . $pid . '-' . $time;

        if($cartCollection->isEmpty()) return redirect()->route('front.home-page');


        $provinces = Vanthao03596Province::all();
        $districts = District::all();
        $wards = Ward::all();

        return view('site.orders.checkout', compact('cartCollection', 'total', 'provinces', 'districts', 'wards', 'orderCode'));
    }

    // áp dụng mã giảm giá (boolean)
    public function applyVoucher(Request $request) {
        $voucher = Voucher::query()->where('code', $request->code)->first();
        $cartCollection = \Cart::getContent();
        $total_price = \Cart::getTotal();
        $total_qty = \Cart::getContent()->sum('quantity');
        // dd($total_price, $total_qty, $voucher);
        if(isset($voucher) && (($total_price >= $voucher->limit_bill_value && $voucher->limit_bill_value > 0) || ($voucher->limit_product_qty > 0 && $total_qty >= $voucher->limit_product_qty))) {
            return Response::json(['success' => true, 'voucher' => $voucher, 'message' => 'Áp dụng mã giảm giá thành công']);
        }
        return Response::json(['success' => false, 'message' => 'Không đủ điều kiện áp mã giảm giá']);
    }

    // submit đặt hàng
    public function checkoutSubmit(Request $request)
    {
        DB::beginTransaction();
        try {
            $translate = [
                'customer_id.required' => 'Thiếu thông tin khách hàng',
            ];

            $validate = Validator::make(
                $request->all(),
                [
                    'customer_id' => 'required',
                ],
                $translate
            );

            $json = new \stdClass();

            if ($validate->fails()) {
                $json->success = false;
                $json->errors = $validate->errors();
                $json->message = "Thao tác thất bại!";
                return Response::json($json);
            }
            $cartList = \Cart::session('cartList');

            $total_price = $cartList->getTotal();

            $order = Order::query()->create([
                'customer_id' => $request->customer_id,
                'total_after_discount' => $total_price,
                'code' => $request->order_code,
            ]);

            foreach ($request->items as $item) {
                $detail = new OrderDetail();
                $detail->order_id = $order->id;
                $detail->customer_id = $request->customer_id;
                $product_id = is_numeric($item['id']) ? $item['id'] : Product::query()->where('slug', $item['attributes']['slug'])->first()->id;
                $detail->post_id = $product_id;
                $detail->qty = $item['quantity'];
                $detail->price = $item['price'];
                $detail->status = 1;
                $detail->save();
            }


            $cartList->clear();

            session(['order_id' => $order->id]);


            DB::commit();
            return Response::json(['success' => true, 'order_code' => $order->code, 'message' => 'Đặt hàng thành công']);
        } catch (\Exception $exception) {
            DB::rollBack();
            dd($exception->getMessage());
        }

    }

    // trả về trang đặt hàng thành công
    public function checkoutSuccess(Request $request)
    {
        if (!session()->has('order_id')) {
            return redirect()->route('front.home-page');
        }

        $orderId = session('order_id');
        $order = Order::query()->with('details', 'details.post', 'details.post.image')->find($orderId);
        session()->forget('order_id');

        return view('site.orders.checkout_success', compact('order'));
    }

}
