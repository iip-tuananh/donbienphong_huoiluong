@extends('layouts.main')

@section('title')
    Chỉnh sửa cửa hàng
@endsection

@section('page_title')
    Chỉnh sửa cửa hàng
@endsection

@section('title')
    Chỉnh sửa cửa hàng
@endsection
@section('content')
    <div ng-controller="EditStore" ng-cloak>
        @include('admin.stores.form')
    </div>
@endsection
@section('script')
    @include('admin.stores.Store')
    <script>
        app.controller('EditStore', function ($rootScope, $scope, $http) {
            $scope.form = new Store(@json($object), {scope: $scope});
            $scope.provinces = @json(\Vanthao03596\HCVN\Models\Province::get());
            $scope.loading = {};
            $scope.loading.submit = false;

            // Submit Form sửa
            $scope.submit = function () {
                let url = "/admin/stores/" + $scope.form.id + "/update";
                $scope.loading.submit = true;
                $scope.form.lat = $('#latitude').val() ;
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
                            $('#edit-store').modal('hide');
                            toastr.success(response.message);
                            datatable.ajax.reload();
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
        let lat = {{$object->lat ? floatval($object->lat) : 21.014066}},
            lng = {{$object->long ? floatval($object->long) : 105.803282}};


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

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB7DU37dL6MMP6g0LDFoU109Ps3YQbeH00&callback=initAutocomplete&v=3"
            async defer></script>
@endsection
