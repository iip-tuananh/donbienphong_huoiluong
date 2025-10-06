@extends('layouts.main')

@section('title')
    Thêm mới đơn vị thành viên
@endsection

@section('page_title')
    Thêm mới đơn vị thành viên
@endsection

@section('title')
    Thêm mới đơn vị thành viên
@endsection
@section('content')
    <div ng-controller="CreateStore" ng-cloak>
        @include('admin.stores.form')
    </div>
@endsection
@section('script')
    @include('admin.stores.Store')
    <script>
        app.controller('CreateStore', function ($scope, $http) {
            $scope.form = new Store({}, {scope: $scope});
            $scope.loading = {};
            $scope.provinces = @json(\Vanthao03596\HCVN\Models\Province::get());

            // Submit Form tạo mới
            $scope.submit = function () {
                let url = "{!! route('stores.store') !!}";;
                $scope.loading.submit = true;
                $scope.form.lat = $('#latitude').val();
                $scope.form.long = $('#longitude').val();

                $.ajax({
                    type: "POST",
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    data: $scope.form.submit_data,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            $('#create-store').modal('hide');
                            toastr.success(response.message);
                            window.location.href = "{{ route('stores.index') }}";
                            $scope.errors = null;
                        } else {
                            $scope.errors = response.errors;
                            toastr.warning(response.message);
                        }
                    },
                    error: function () {
                        toastr.error('Đã có lỗi xảy ra');
                    },
                    complete: function () {
                        $scope.loading.submit = false;
                        $scope.$applyAsync();
                    },
                });
            }
        })
    </script>

    <script>
        let lat = {{(@$contact['latitude']) ? floatval($contact['latitude']) : 21.014066}},
            lng = {{(@$contact['longitude']) ? floatval($contact['longitude']) : 105.803282}};

        function initAutocomplete() {
            const map = new google.maps.Map(document.getElementById('map'), {
                zoom: 14,
                // center: new google.maps.LatLng(21.014066, 105.803282)
                center: new google.maps.LatLng(lat, lng)
            });

            let marker = new google.maps.Marker({
                map: map
            });
            const loc = new google.maps.LatLng(lat, lng);
            marker.setPosition(loc);
            map.panTo(loc);
            var geocoder = new google.maps.Geocoder();
            google.maps.event.addListener(map, 'click', function (event) {
                marker.setPosition(event.latLng);
                map.panTo(event.latLng);

                $('#latitude').val(event.latLng.lat())
                $('#longitude').val(event.latLng.lng())
                geocoder.geocode({
                    'latLng': event.latLng
                }, function (results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                        if (results[0]) {
                            $('#address').html(results[0].formatted_address)
                        }
                    }
                });
            });
        }
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB7DU37dL6MMP6g0LDFoU109Ps3YQbeH00&callback=initAutocomplete"
            async defer></script>
@endsection
