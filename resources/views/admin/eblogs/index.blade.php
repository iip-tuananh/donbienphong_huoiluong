@extends('layouts.main')

@section('css')
    <style>

        #sortable-list {
            width: 100%;
            max-width: 600px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        #sortable-list div {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            position: relative;
            background-color: #f0f0f0; /* Màu sắc mặc định cho các khối */
        }

        #sortable-list div:hover {
            background-color: #e0e0e0; /* Màu sắc khi di chuột qua */
        }

        .edit-icon {
            position: absolute;
            top: 5px;
            right: 5px;
            cursor: pointer;
        }


    </style>
@endsection

@section('page_title')
Quản lý khối nội dung trang e-brochure
@endsection

@section('title')
Quản lý khối nội dung trang e-brochure
@endsection

@section('buttons')
<a href="javascript:void(0)" class="btn btn-outline-success" data-toggle="modal" data-target="#create-banner" class="btn btn-info" ng-click="errors = null"><i class="fa fa-plus"></i> Thêm mới</a>
@endsection
@section('content')
<div ng-cloak>
    <div class="row" ng-controller="Banners">
        <div class="col-12">
            <div id="sortable-list">
                <div data-id="<% showroom.id %>" ng-repeat="showroom in showrooms">
                    <% showroom.title %> <span class="edit-icon" ng-click="editBlock( showroom.id )">✏️</span>
                </div>
            </div>
        </div>

        {{-- Form tạo mới --}}
        @include('admin.eblogs.create')
        @include('admin.eblogs.edit')

    </div>

</div>
@endsection

@section('script')
@include('admin.eblogs.Showroom')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script>
    app.controller('Banners', function ($rootScope, $scope, $http) {
        const sortableList = document.getElementById('sortable-list');
        $scope.showrooms = @json($showrooms);

        new Sortable(sortableList, {
            onEnd: function (evt) {
                updateBlockOrder(evt.oldIndex, evt.newIndex);
            },
        });

        function updateBlockOrder(oldIndex, newIndex) {
            const showrooms = Array.from(sortableList.children).map((item) => item.dataset.id);

            console.log(showrooms)
            $.ajax({
                type: 'POST',
                url: "{!! route('e-brochure.updateOrderShowroom') !!}",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: {
                    showrooms: showrooms
                },

                success: function(response) {
                    if (response.success) {

                        toastr.success('Thao tác thành công');
                    } else {
                        toastr.warning(response.message);
                        $scope.errors = response.errors;
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

        $scope.editBlock = function (blockId) {
            $.ajax({
                type: 'GET',
                url: "/admin/e-brochure/" + blockId + "/getDataForEdit",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: blockId,

                success: function(response) {
                    if (response.success) {

                        $scope.booking = response.data;

                        $rootScope.$emit("editBanner", $scope.booking);
                    } else {
                        toastr.warning(response.message);
                        $scope.errors = response.errors;
                    }
                },
                error: function(e) {
                    toastr.error('Đã có lỗi xảy ra');
                },
                complete: function() {
                    $scope.loading.submit = false;
                    $scope.$applyAsync();
                }
            });
            $scope.errors = null;
            $scope.$apply();
            $('#edit-banner').modal('show');
        }

    })

</script>
@include('partial.confirm')
@endsection
