@extends('layouts.main')

@section('css')
@endsection

@section('title')
Quản lý bài viết
@endsection

@section('page_title')
Quản lý bài viết
@endsection

@section('content')
<div  ng-cloak>
	<div class="row" ng-controller="Posts">
		<div class="col-12">
			<div class="card">
				<!-- /.card-header -->
				<div class="card-body">
					<table id="table-list">
					</table>
				</div>
			</div>
		</div>

{{--        <div class="modal fade" id="add-to-category-special" tabindex="-1" role="dialog" aria-hidden="true">--}}
{{--            <div class="modal-dialog">--}}
{{--                <div class="modal-content">--}}
{{--                    <div class="modal-header">--}}
{{--                        <h4 class="semi-bold">Thêm vào danh mục đặc biệt</h4>--}}
{{--                    </div>--}}
{{--                    <div class="modal-body">--}}
{{--                        <div class="row">--}}
{{--                            <div class="col-sm-12">--}}
{{--                                <div class="row">--}}
{{--                                    <div class="col-md-12 col-sm-12 col-xs-12">--}}
{{--                                        <div class="form-group custom-group">--}}
{{--                                            <label class="form-label required-label">Danh mục đặc biệt</label>--}}

{{--                                            <ui-select remove-selected="false" multiple ng-model="post.category_special_ids">--}}
{{--                                                <ui-select-match placeholder="Chọn danh mục đặc biệt">--}}
{{--                                                    <% $item.name %>--}}
{{--                                                </ui-select-match>--}}
{{--                                                <ui-select-choices--}}
{{--                                                    repeat="item.id as item in (categorieSpeicals | filter: $select.search)">--}}
{{--                                                    <span ng-bind="item.name"></span>--}}
{{--                                                </ui-select-choices>--}}
{{--                                            </ui-select>--}}

{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                    </div>--}}
{{--                    <div class="modal-footer">--}}
{{--                        <button type="button" class="btn btn-success btn-cons" ng-click="submit()"--}}
{{--                                ng-disabled="loading.submit">--}}
{{--                            <i ng-if="!loading.submit" class="fa fa-save"></i>--}}
{{--                            <i ng-if="loading.submit" class="fa fa-spin fa-spinner"></i>--}}
{{--                            Lưu--}}
{{--                        </button>--}}
{{--                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i--}}
{{--                                class="fas fa-window-close"></i> Hủy--}}
{{--                        </button>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <!-- /.modal-content -->--}}
{{--            </div>--}}
{{--            <!-- /.modal-dialog -->--}}
{{--        </div>--}}

	</div>
</div>
@endsection
@section('script')
<script>
    let datatable = new DATATABLE('table-list', {
        ajax: {
            url: '/admin/abouts/searchData',
            data: function (d, context) {
                DATATABLE.mergeSearch(d, context);
            }
        },
        columns: [
            {data: 'DT_RowIndex', orderable: false, title: "STT"},
            {data: 'name',title: 'Tiêu đề'},
            {data: 'cate_id', title: 'Danh mục'},
            {data: 'created_at', title: "Ngày cập nhật"},
            {data: 'updated_by', title: "Người cập nhật"},
            {
                data: 'image', title: "Hình ảnh", orderable: false, className: "text-center",
            },
            {data: 'action', orderable: false, title: "Hành động"}
        ],
        search_columns: [
            {data: 'name', search_type: "text", placeholder: "Tiêu đề"},
            {
                data: 'cate_id', search_type: "select", placeholder: "Danh mục",
                column_data: @json(App\Model\Admin\PostCategory::getForSelect(2))
            },
        ],
        search_by_time: false,
        @if(Auth::user()->type == App\Model\Common\User::SUPER_ADMIN || Auth::user()->type == App\Model\Common\User::QUAN_TRI_VIEN)
        create_link: "{{ route('abouts.create') }}"
        @endif
    }).datatable;

    app.controller('Posts', function ($scope, $rootScope, $http) {

        $scope.arrayInclude = arrayInclude;
        $scope.loading = {};


    })
</script>
@include('partial.confirm')
@endsection
