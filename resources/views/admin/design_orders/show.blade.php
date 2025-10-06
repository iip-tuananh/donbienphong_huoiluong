@extends('layouts.main')

@section('css')
@endsection

@section('page_title')
    Chi tiết đơn thiết kế
@endsection

@section('title')
    Chi tiết đơn thiết kế
@endsection

@section('buttons')
@endsection

@section('content')

<div ng-controller="DesignOrder" ng-cloak>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6>Thông tin chung</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Khách hàng: <% form.customer_name %> </label>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Email: <% form.customer_email %></label>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">SĐT: <% form.customer_phone %></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Địa chỉ: <% form.customer_address %> </label>
                            </div>
                        </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Trạng thái: <% getStatus(form.status) %></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Ngày đặt hàng: <% form.created_at | date: 'dd/MM/yyyy HH:mm' %></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Ghi chú: </label>
                                <textarea id="my-textarea" class="form-control" rows="3"><% form.customer_note %></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Chi tiết thiết kế</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <table class="table table-bordered table-hover table-responsive">
                            <thead>
                                <tr>
                                    <th>Tên sản phẩm</th>
                                    <th>Mã màu sắc</th>
                                    <th>Kích thước chiều dài</th>
                                    <th>Kích thước chiều rộng</th>
                                    <th>Kích thước chiều cao</th>
                                    <th>Phân loại</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="text-center"><% form.product_name %></td>
                                    <td class="text-center"><% form.product_color %></td>
                                    <td class="text-center"><% form.product_size_length %></td>
                                    <td class="text-center"><% form.product_size_width %></td>
                                    <td class="text-center"><% form.product_size_height %></td>
                                    <td class="text-center"><% form.product_attributes %></td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>Thành phần</th>
                                    <th>Hình ảnh</th>
                                    <th>Ảnh thành phần</th>
                                    <th>Nội dung text</th>
                                    <th>Mã màu sắc</th>
                                    <th>Phông chữ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Mặt trước -->
                                <tr ng-if="front_layers.layers.length > 0">
                                    <td style="vertical-align: middle;" class="text-center" rowspan="<% front_layers.layers.length %>">Mặt trước</td>
                                    <td style="vertical-align: middle;" class="text-center" rowspan="<% front_layers.layers.length %>">
                                        <img ng-src="<% front_layers.image %>" width="200" height="auto" alt="Front Layer" class="img-fluid">
                                    </td>
                                    <td style="vertical-align: middle;" class="text-center"><img ng-src="<% front_layers.layers[0].image.path %>" width="100" height="auto" alt="Front Layer" class="img-fluid" ng-if="front_layers.layers[0].image"></td>
                                    <td style="vertical-align: middle;" class="text-center"><span style="font-weight: bold; font-size: 24px;" ng-if="front_layers.layers[0].type == 'text'"><% front_layers.layers[0].design_text %></span></td>
                                    <td style="vertical-align: middle;" class="text-center"><span ng-if="front_layers.layers[0].type == 'text'"><% front_layers.layers[0].design_color %></span></td>
                                    <td style="vertical-align: middle;" class="text-center">
                                        <span ng-if="front_layers.layers[0].design_font"><span style="font-weight: bold;">Phông chữ: </span><% front_layers.layers[0].design_font %></span><br>
                                        <span ng-if="front_layers.layers[0].design_font_style"><span style="font-weight: bold;">Định dạng: </span>
                                        <% front_layers.layers[0].design_font_style != 'normal' ?
                                            (front_layers.layers[0].design_font_style == 'italic' ? 'Nghiêng' : 'In đậm') : 'Thường' %></span>
                                    </td>
                                </tr>
                                <tr ng-repeat="layer in front_layers.layers.slice(1) track by $index">
                                    <td style="vertical-align: middle;" class="text-center"><img ng-src="<% layer.image.path %>" width="100" height="auto" alt="Front Layer" class="img-fluid" ng-if="layer.image"></td>
                                    <td style="vertical-align: middle;" class="text-center"><span style="font-weight: bold; font-size: 24px;" ng-if="layer.type == 'text'"><% layer.design_text %></span></td>
                                    <td style="vertical-align: middle;" class="text-center"><span ng-if="layer.type == 'text'"><% layer.design_color %></span></td>
                                    <td style="vertical-align: middle;" class="text-center">
                                        <span ng-if="layer.design_font"><span style="font-weight: bold;">Phông chữ: </span><% layer.design_font %></span><br>
                                        <span ng-if="layer.design_font_style"><span style="font-weight: bold;">Định dạng: </span>
                                        <% layer.design_font_style != 'normal' ? (layer.design_font_style == 'italic' ? 'Nghiêng' : 'In đậm') : 'Thường' %></span>
                                    </td>
                                </tr>

                                <!-- Mặt sau -->
                                <tr ng-if="back_layers.layers.length > 0">
                                    <td style="vertical-align: middle;" class="text-center" rowspan="<% back_layers.layers.length %>">Mặt sau</td>
                                    <td style="vertical-align: middle;" class="text-center" rowspan="<% back_layers.layers.length %>">
                                        <img ng-src="<% back_layers.image %>" width="200" height="auto" alt="Back Layer" class="img-fluid">
                                    </td>
                                    <td style="vertical-align: middle;" class="text-center"><img ng-src="<% back_layers.layers[0].image.path %>" width="100" height="auto" alt="Back Layer" class="img-fluid" ng-if="back_layers.layers[0].image"></td>
                                    <td style="vertical-align: middle;" class="text-center"><span style="font-weight: bold; font-size: 24px;" ng-if="back_layers.layers[0].type == 'text'"><% back_layers.layers[0].design_text %></span></td>
                                    <td style="vertical-align: middle;" class="text-center"><span ng-if="back_layers.layers[0].type == 'text'"><% back_layers.layers[0].design_color %></span></td>
                                    <td style="vertical-align: middle;" class="text-center">
                                        <span ng-if="back_layers.layers[0].design_font"><span style="font-weight: bold;">Phông chữ: </span><% back_layers.layers[0].design_font %><br></span>
                                        <span ng-if="back_layers.layers[0].design_font_style"><span style="font-weight: bold;">Định dạng: </span>
                                        <% back_layers.layers[0].design_font_style != 'normal' ?
                                            (back_layers.layers[0].design_font_style == 'italic' ? 'Nghiêng' : 'In đậm') : 'Thường' %></span>
                                    </td>
                                </tr>
                                <tr ng-repeat="layer in back_layers.layers.slice(1) track by $index">
                                    <td style="vertical-align: middle;" class="text-center"><img ng-src="<% layer.image.path %>" width="100" height="auto" alt="Back Layer" class="img-fluid" ng-if="layer.image"></td>
                                    <td style="vertical-align: middle;" class="text-center"><span style="font-weight: bold; font-size: 24px;" ng-if="layer.type == 'text'"><% layer.design_text %></span></td>
                                    <td style="vertical-align: middle;" class="text-center"><span ng-if="layer.type == 'text'"><% layer.design_color %></span></td>
                                    <td style="vertical-align: middle;" class="text-center">
                                        <span ng-if="layer.design_font"><span style="font-weight: bold;">Phông chữ: </span><% layer.design_font %></span><br>
                                        <span ng-if="layer.design_font_style"><span style="font-weight: bold;">Định dạng: </span>
                                        <% layer.design_font_style != 'normal' ? (layer.design_font_style == 'italic' ? 'Nghiêng' : 'In đậm') : 'Thường' %></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <hr>

    <div class="text-right">
        <a href="{{ route('design_orders.index') }}" class="btn btn-danger btn-cons">
            <i class="fa fa-remove"></i> Quay lại
        </a>
    </div>
</div>
@endsection

@section('script')
    @include('admin.design_orders.DesignOrder')

    <script>
        app.controller('DesignOrder', function ($scope, $http) {
            $scope.form = new DesignOrder(@json($order), {scope: $scope});
            $scope.front_layers = @json($front_layers);
            $scope.back_layers = @json($back_layers);
            $scope.statuses = @json(\App\Model\Admin\DesignOrder::STATUSES);
            $scope.$applyAsync();

            $scope.getStatus = function (status) {
                let obj = $scope.statuses.find(val => val.id == status);
                return obj.name;
            }

        });
    </script>
@endsection
