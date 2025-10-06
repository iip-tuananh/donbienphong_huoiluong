@extends('site.layouts.master')
@section('title')
    Giỏ hàng
@endsection

@section('css')

@endsection

@section('content')
    <div class="content" ng-controller="CartController">
        <div class="breadcrumbs-header fl-wrap">
            <div class="container">
                <div class="breadcrumbs-header_url">
                    <a href="{{ route('front.home-page') }}">Trang chủ</a><span>Giỏ hàng</span>
                </div>
                <div class="scroll-down-wrap">
                    <div class="mousey">
                        <div class="scroller"></div>
                    </div>
                    <span>Scroll Down To Discover</span>
                </div>
            </div>
            <div class="pwh_bg"></div>
        </div>
        <!--section   -->
        <section>
            <div class="container">
                <!-- CHECKOUT TABLE -->
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="cart-title">Giỏ hàng: </h4>
                        <table class="table table-border checkout-table">
                            <tbody>
                            <tr>
                                <th class="hidden-xs">Hình ảnh</th>
                                <th>Bài viết</th>
                                <th class="hidden-xs">Giá</th>
                                <th>Số lượng</th>
                                <th>Thành tiền</th>
                                <th></th>
                            </tr>
                            <tr ng-repeat="item in items">
                                <td class="hidden-xs">
                                    <a href="#"><img src="<% item.attributes.image %>" alt="" class="respimg"></a>
                                </td>
                                <td>
                                    <h5 class="product-name"><% item.name %></h5>
                                </td>
                                <td class="hidden-xs">
                                    <h5 class="order-money"><% (+item.price > 0) ? ((+item.price) | number) + '₫' : 'Liên hệ' %> </h5>
                                </td>
                                <td>
                                    <input type="number" name="cartin1" value="1" max="50" min="1" class="order-count">
                                </td>
                                <td>
                                    <h5 class="order-money"> <% (+item.price > 0)
                                        ? (((+item.price) * (+item.quantity || 1)) | number) + '₫'
                                        : 'Liên hệ' %> </h5>
                                </td>
                                <td class="pr-remove">
                                    <a href="#" title="Remove" ng-click="removeItem(item.id)"><i class="fas fa-times"></i></a>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                        <!-- COUPON -->

                        <!-- /COUPON -->
                    </div>
                    <div class="col-md-4">
                        <!-- CART TOTALS  -->
                        <div class="cart-totals  fl-wrap">
                            <h3>Tổng tiền</h3>
                            <table class="total-table">
                                <tbody>
                                <tr>
                                    <th>Tạm tính:</th>
                                    <td><% total | number%>₫ </td>
                                </tr>
                                <tr>
                                    <th>Tổng thanh toán:</th>
                                    <td><% total | number%>₫ </td>
                                </tr>
                                </tbody>
                            </table>

                            <a href="/thanh-toan">
                                <button type="button" class="cart-totals_btn color-bg">Thanh toán</button>
                            </a>
                        </div>
                        <!-- /CART TOTALS  -->
                    </div>
                </div>
                <!-- /CHECKOUT TABLE -->
            </div>
        </section>
    </div>

@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.cart-item').forEach(item => {
            item.addEventListener('click', e => {
                if (e.target.classList.contains('qty-btn')) {
                    const input = item.querySelector('.qty-input');
                    console.log( input.value)
                    let val = parseInt(input.value,10) || 1;
                    if (e.target.classList.contains('minus')) val = Math.max(1, val - 1);
                    else val++;
                    input.value = val;
                    // updateCart();
                }
            });
            // khi gõ số trực tiếp
            item.querySelector('.qty-input').addEventListener('change', e => {
                if (e.target.value < 1) e.target.value = 1;
                // updateCart();
            });
        });
    </script>

     <script>
        app.controller('CartController', function($scope, cartItemSync, $interval, $rootScope) {
            $scope.items = @json($cartCollection);
            $scope.total_qty = "{{ $total_qty }}";
            $scope.checkCart = true;
            $scope.total = "{{$total_price}}";

            $scope.countItem = Object.keys($scope.items).length;

            jQuery(document).ready(function() {
                if ($scope.total_qty == 0) {
                    $scope.checkCart = false;
                    $scope.$applyAsync();
                }
            })

            $scope.changeQty = function(qty, product_id) {
                updateCart(qty, product_id)
            }

            $scope.incrementQuantity = function(product) {
                product.quantity = Math.min(product.quantity + 1, 9999);
            };

            $scope.decrementQuantity = function(product) {
                product.quantity = Math.max(product.quantity - 1, 0);
            };

            function updateCart(qty, product_id) {
                jQuery.ajax({
                    type: 'POST',
                    url: "{{ route('cart.update.item') }}",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    data: {
                        product_id: product_id,
                        qty: qty
                    },
                    success: function(response) {
                        if (response.success) {
                            $scope.items = response.items;
                            $scope.total = response.total;
                            $scope.total_qty = response.count;
                            $interval.cancel($rootScope.promise);

                            $rootScope.promise = $interval(function() {
                                cartItemSync.items = response.items;
                                cartItemSync.total = response.total;
                                cartItemSync.count = response.count;
                            }, 1000);

                            $scope.$applyAsync();
                        }
                    },
                    error: function(e) {
                        toastr.error('Đã có lỗi xảy ra');
                    },
                    complete: function() {
                        $scope.$applyAsync();
                    }
                });
            }

            $scope.removeItem = function(post_id) {
                jQuery.ajax({
                    type: 'GET',
                    url: "{{ route('cart.remove.item') }}",
                    data: {
                        post_id: post_id
                    },
                    success: function(response) {
                        if (response.success) {
                            $scope.items = response.items;
                            $scope.total = response.total;
                            $scope.total_qty = response.count;
                            if ($scope.total_qty == 0) {
                                $scope.checkCart = false;
                            }

                            $interval.cancel($rootScope.promise);

                            $rootScope.promise = $interval(function() {
                                cartItemSync.items = response.items;
                                cartItemSync.total = response.total;
                                cartItemSync.count = response.count;
                            }, 1000);

                            $scope.countItem = Object.keys($scope.items).length;

                            $scope.$applyAsync();
                        }
                    },
                    error: function(e) {
                        toastr.error('Đã có lỗi xảy ra');
                    },
                    complete: function() {
                        $scope.$applyAsync();
                    }
                });
            }
        });
    </script>
@endpush
