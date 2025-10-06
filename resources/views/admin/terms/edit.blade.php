@extends('layouts.main')

@section('css')

@endsection

@section('title')
Chỉnh sửa điều khoản
@endsection

@section('page_title')
    Chỉnh sửa điều khoản
@endsection

@section('content')
<div ng-controller="Polivicy" ng-cloak>
  @include('admin.terms.form')
</div>
@endsection
@section('script')
    @include('admin.terms.Polivicy')
    @include('admin.terms.PolivicyDetail')
    <script>
        // Hàm cập nhật mũi tên theo trạng thái của khối collapse
        function updateToggleArrow(collapseDiv) {
            var cardHeader = $(collapseDiv).prev('.card-header');
            var arrowSpan = cardHeader.find('.toggle-arrow');
            if ($(collapseDiv).hasClass('show')) {
                arrowSpan.html('▲'); // Khi đang mở, hiển thị mũi tên lên
            } else {
                arrowSpan.html('▼'); // Khi đang đóng, hiển thị mũi tên xuống
            }
        }

        // Lắng nghe sự kiện khi khối collapse mở ra
        $('#blocks-container').on('shown.bs.collapse', '.collapse', function () {
            updateToggleArrow(this);
        });

        // Lắng nghe sự kiện khi khối collapse đóng lại
        $('#blocks-container').on('hidden.bs.collapse', '.collapse', function () {
            updateToggleArrow(this);
        });
    </script>
<script>
    app.controller('Polivicy', function ($scope, $http) {
        $scope.form = new Polivicy(@json($object), {scope: $scope});

        $scope.loading = {};

        $scope.submit = function(publish = 0) {
            $scope.loading.submit = true;
            let data = $scope.form.submit_data;
            console.log( $scope.form.submit_data)
            $.ajax({
                type: 'POST',
                url: "/admin/terms/" + "{{ $object->id }}" + "/update",
                headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: data,
                success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
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
        }

        @include('admin.polivicy.formJs');
    });
</script>
@endsection
