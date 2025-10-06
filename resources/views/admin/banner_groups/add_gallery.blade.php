@extends('layouts.main')

@section('css')

@endsection

@section('title')
    Cập nhật hình ảnh
@endsection

@section('page_title')
    Cập nhật hình ảnh
@endsection

@section('content')
    <div ng-controller="Post" ng-cloak>
        <style>
            .gallery-item {
                padding: 5px;
                padding-bottom: 0;
                border-radius: 2px;
                border: 1px solid #bbb;
                min-height: 100px;
                height: 100%;
                position: relative;
            }

            .gallery-item .remove {
                position: absolute;
                top: 5px;
                right: 5px;
            }

            .gallery-item .drag-handle {
                position: absolute;
                top: 5px;
                left: 5px;
                cursor: move;
            }

            .gallery-item:hover {
                background-color: #eee;
            }

            .gallery-item .image-chooser img {
                max-height: 150px;
            }

            .gallery-item .image-chooser:hover {
                border: 1px dashed green;
            }
        </style>

        <div class="row">
            <div class="col-lg-2">
            </div>

            <style>
                .gallery-item{ position:relative; border:1px solid #e9ecef; border-radius:8px; padding:8px; background:#fff; }
                .gallery-item .remove{ position:absolute; top:6px; right:6px; }
                .gallery-item img{ width:100%; height:180px; object-fit:cover; border-radius:6px; }
                .caption-field input{ background:#f8fafc; }
                @media (max-width: 575.98px){
                    .gallery-item img{ height:150px; }
                }

            </style>
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header text-center ">
                        <h5>{{ $object->title }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group text-center">
                            <label for="">Thư viện ảnh</label>
                            <div class="row gallery-area border">
{{--                                <div class="col-md-4 p-2" ng-repeat="g in form.galleries">--}}
{{--                                    <div class="gallery-item">--}}
{{--                                        <button class="btn btn-sm btn-danger remove" ng-click="form.removeGallery($index)">--}}
{{--                                            <i class="fa fa-times mr-0"></i>--}}
{{--                                        </button>--}}
{{--                                        <div class="form-group">--}}
{{--                                            <div class="img-chooser" title="Chọn ảnh">--}}
{{--                                                <label for="<% g.image.element_id %>">--}}
{{--                                                    <img ng-src="<% g.image.path %>">--}}
{{--                                                    <input class="d-none" type="file" accept=".jpg,.png,.jpeg" id="<% g.image.element_id %>">--}}
{{--                                                </label>--}}
{{--                                            </div>--}}
{{--                                            <span class="invalid-feedback d-block" role="alert" ng-if="!errors['galleries.' + $index + '.image_obj']">--}}
{{--                                <strong>--}}
{{--                                    <% errors['galleries.' + $index + '.image' ][0] %>--}}
{{--                                </strong>--}}
{{--                            </span>--}}
{{--                                            <span class="invalid-feedback d-block" role="alert" ng-if="errors && errors['galleries.' + $index + '.image_obj']">--}}
{{--                                <strong>--}}
{{--                                    <% errors['galleries.' + $index + '.image_obj' ][0] %>--}}
{{--                                </strong>--}}
{{--                            </span>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}



                                <div class="col-md-4 p-2" ng-repeat="g in form.galleries">
                                    <div class="gallery-item">
                                        <button class="btn btn-sm btn-danger remove" ng-click="form.removeGallery($index)">
                                            <i class="fa fa-times mr-0"></i>
                                        </button>

                                        <div class="form-group mb-2">
                                            <div class="img-chooser" title="Chọn ảnh">
                                                <label for="<% g.image.element_id %>">
                                                    <img ng-src="<% g.image.path %>">
                                                    <input class="d-none" type="file" accept=".jpg,.png,.jpeg" id="<% g.image.element_id %>">
                                                </label>
                                            </div>

                                            <!-- errors ảnh -->
                                            <span class="invalid-feedback d-block" role="alert" ng-if="!errors['galleries.' + $index + '.image_obj']">
        <strong><% errors['galleries.' + $index + '.image' ][0] %></strong>
      </span>
                                            <span class="invalid-feedback d-block" role="alert" ng-if="errors && errors['galleries.' + $index + '.image_obj']">
        <strong><% errors['galleries.' + $index + '.image_obj' ][0] %></strong>
      </span>
                                        </div>


                                        <div class="form-group mb-0 caption-field">
                                            <label class="sr-only" for="cap-<% $index %>">Link liên kết</label>
                                            <input
                                                id="cap-<% $index %>"
                                                type="text"
                                                class="form-control form-control-sm"
                                                placeholder="Nhập link ảnh"
                                                ng-model="g.caption"
                                                ng-attr-name="galleries[<% $index %>][caption]">


                                            <!-- errors caption -->
                                            <span class="invalid-feedback d-block" role="alert" ng-if="errors && errors['galleries.' + $index + '.caption']">
        <strong><% errors['galleries.' + $index + '.caption' ][0] %></strong>
      </span>
                                        </div>
                                    </div>
                                </div>



                                <div class="col-md-4 p-2">
                                    <label class="gallery-item d-flex align-items-center justify-content-center cursor-pointer" for="gallery-chooser">
                                        <i class="fa fa-plus fa-2x text-secondary"></i>
                                    </label>
                                    <input class="d-none" type="file" accept=".jpg,.png,.jpeg" id="gallery-chooser" multiple>
                                </div>
                            </div>
                            <span class="invalid-feedback d-block" role="alert" ng-if="errors && errors['galleries']">
                                <strong>
                                    <% errors.galleries[0] %>
                                </strong>
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-2">
            </div>

        </div>

        <div class="text-right mt-3">
            <button type="submit" class="btn btn-success px-4" ng-click="submit(0)" ng-disabled="loading.submit">
                <i ng-if="!loading.submit" class="fa fa-save"></i>
                <i ng-if="loading.submit" class="fa fa-spin fa-spinner"></i>
                Lưu
            </button>
        </div>

    </div>
@endsection
@section('script')
    @include('admin.banner_groups.BannerGroup')
    <script>
        app.controller('Post', function ($scope, $http, $timeout) {
            $scope.form = new BannerGroup(@json($object), {scope: $scope});
            $scope.loading = {};
            @include('admin.banner_groups.formJs')

            $scope.submit = function () {
                $scope.loading.submit = true;
                let data = $scope.form.submit_data;
                $.ajax({
                    type: 'POST',
                    url: "/admin/banner-groups/" + "{{ $object->id }}" + "/update",
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.warning(response.message);
                            $scope.errors = response.errors;
                        }
                    },
                    error: function (e) {
                        toastr.error('Đã có lỗi xảy ra');
                    },
                    complete: function () {
                        $scope.loading.submit = false;
                        $scope.$applyAsync();
                    }
                });
            }
        });
    </script>
@endsection
