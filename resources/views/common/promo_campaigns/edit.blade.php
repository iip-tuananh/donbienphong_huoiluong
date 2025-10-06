@extends('layouts.main')

@section('css')

@endsection

@section('title')
Chỉnh sửa chương trình khuyến mãi
@endsection
@section('content')
<div ng-controller="PromoCampaign" ng-cloak>
  @include('common.promo_campaigns.form')
</div>
@endsection
@section('script')
@include('partial.classes.common.PromoCampaign')
<script>
  app.controller('PromoCampaign', function ($scope, $http) {
    $scope.form = new PromoCampaign(@json($object), {scope: $scope});

    @include('common.promo_campaigns.formJs')

    $scope.submit = function() {
      $scope.loading.submit = true;
      $.ajax({
        type: 'POST',
        url: "{!! route('PromoCampaign.update', $object->id) !!}",
        headers: {
          'X-CSRF-TOKEN': CSRF_TOKEN
        },
        data: $scope.form.submit_data,
        success: function(response) {
          if (response.success) {
            toastr.success(response.message);
            window.location.href = "{{ route('PromoCampaign.index') }}";
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
  });
</script>
@endsection
