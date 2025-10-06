@extends('layouts.main')

@section('title')
Quản lý danh mục dịch vụ
@endsection

@section('page_title')
Quản lý danh mục dịch vụ
@endsection

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/nested.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.nestable.css') }}">
@endsection
@section('buttons')
<a href="{{ route('ServiceCategory.create') }}" class="btn btn-outline-success btn-sm" class="btn btn-info"><i class="fa fa-plus"></i> Thêm mới</a>
@endsection
@section('content')
<div ng-cloak>
  <div class="row" ng-controller="PostCategory">
    <div class="col-12">
      <div class="card">
        <div class="nested-box">
          <div class="col-lg-12 row-button">
            <menu id="nestable-menu">
              <button type="button" class="btn-outline-primary btn-sm" data-action="expand-all">Mở rộng <i class="fa fa-plus"></i></button>
              <button type="button" class="btn-outline-danger btn-sm" data-action="collapse-all">Thu gọn <i class=" fa fa-minus"></i></button>
            </menu>
            <input type="hidden" id="id">
          </div>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="wrap bg-white">
              <div class="cf nestable-lists">
                <div class="dd" id="nestable">
                  @php
                  $query = $categories;
                  $ref = [];
                  $items = [];
                  foreach ($query as $data) {
                  $thisRef = &$ref[$data->id];
                  $thisRef['parent_id'] = $data->parent_id;
                  $thisRef['name'] = $data->name;
                  $thisRef['slug'] = $data->slug;
                  $thisRef['id'] = $data->id;

                  if($data->parent_id == 0) {
                  $items[$data->id] = &$thisRef;
                  } else {
                  $ref[$data->parent_id]['child'][$data->id] = &$thisRef;
                  }
                  }
                  function get_menu($items,$class = 'dd-list') {
                  $html = "<ol class=\"".$class."\" id=\"menu-id\">";
                    foreach($items as $key=>$value) {
                    $html.= '<li class="dd-item dd3-item" data-id="'.$value['id'].'">
                      <div class="dd-handle dd3-handle"></div>
                      <div class="dd3-content"> <span id="label_show'.$value['id'].'">'.$value['name'].'</span>
                        <span class="span-right">
                            '.($value['parent_id'] == 0 ? '' : '').'
                          <a class="edit-button" href="'. route('ServiceCategory.edit',$value['id']) .'" id="'.$value['id'].'" label="'.$value['name'].'"><i class="fas fa-pencil-alt"></i></a>
                          <a class="del-button confirm" href="'. route('ServiceCategory.delete',$value['id']) .'" id="'.$value['id'].'"><i class="fa fa-trash"></i></a></span>
                      </div>';
                      if(array_key_exists('child',$value)) {
                      $html .= get_menu($value['child'],'child');
                      }
                      $html .= "
                    </li>";
                    }
                    $html .= "</ol>";
                  return $html;
                  }
                  print get_menu($items);
                  @endphp
                </div>
              </div>
              <p></p>
              <input type="hidden" id="nestable-output">
            </div>
          </div>

        </div>
      </div>
    </div>

      <div class="modal fade" id="create-origin" tabindex="-1" role="dialog" aria-hidden="true"
      >
          <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header">
                      <h4 class="semi-bold">Thêm vào trang chủ</h4>

                  </div>
                  <div class="modal-body">
                      <div class="row">
                          <div class="col-sm-12">
                              <div class="row">
                                  <h6>Danh mục: <% form.name %></h6>
                                  <div class="col-md-12 col-sm-12 col-xs-12">
                                      <div class="form-group custom-group ">
                                          <label class="form-label required-label">Thêm vào trang chủ</label>
                                          <div class="input-group mb-3">
                                              <select class="form-control" ng-model="form.show_home_page">
                                                  <option value="">Chọn danh mục</option>
                                                  <option ng-repeat="s in show_home_page" ng-value="s.value" ng-selected="s.value == form.show_home_page">
                                                      <% s.name %>
                                                  </option>
                                              </select>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="col-md-12 col-sm-12 col-xs-12">
                                      <div class="form-group custom-group">
                                          <label class="form-label required-label">Vị trí</label>
                                          <input class="form-control " type="number" ng-model="form.order_number">
                                          <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.name[0] %></strong>
                    </span>
                                      </div>
                                  </div>

                              </div>
                          </div>
                      </div>

                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-success btn-cons" ng-click="submit()" ng-disabled="loading.submit">
                          <i ng-if="!loading.submit" class="fa fa-save"></i>
                          <i ng-if="loading.submit" class="fa fa-spin fa-spinner"></i>
                          Lưu
                      </button>
                      <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-window-close"></i> Hủy</button>
                  </div>
              </div>
              <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
      </div>
  </div>
</div>
@endsection

@section('script')
<script src="{{ asset('js/jquery.nestable.js') }}"></script>
<script>
  $(document).ready(function()
  {

    var updateOutput = function(e)
    {
      var list   = e.length ? e : $(e.target),
      output = list.data('output');
      if (window.JSON) {
            output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
          } else {
            output.val('JSON browser support required for this demo.');
          }
        };

    // activate Nestable for list 1
    $('#nestable').nestable({
      group: 1
    })
    .on('change', updateOutput);

    // output initial serialised data
    updateOutput($('#nestable').data('output', $('#nestable-output')));
    $('#nestable-menu').on('click', function(e)
    {
      var target = $(e.target),
      action = target.data('action');

      if (action === 'expand-all') {
        $('.dd').nestable('expandAll');
      }

      if (action === 'collapse-all') {
        $('.dd').nestable('collapseAll');
      }

    });
  });
</script>

<script>
  $(document).ready(function(){
    $("#load").hide();

    $('.dd').on('change', function() {
      $("#load").show();
      var dataString = {
        data : $("#nestable-output").val(),
      };
      console.log(dataString);
      var baseURL = window.location.origin;
      $.ajax({
        type: "POST",
        url: baseURL + '/admin/service-categories/nested-sort',
        data: {dataString},
        cache : false,
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        success: function (response) {
          if(response.success) {
            toastr.success(response.message);
          } else {
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

    });

    $(document).on("click",".edit-button",function() {
      var id = $(this).attr('id');
      var label = $(this).attr('label');
      var link = $(this).attr('link');
      $("#id").val(id);
      $("#label").val(label);
      $("#link").val(link);
    });
  });

  app.controller('PostCategory', function ($rootScope, $scope, $http) {
      $scope.loading = {};
      $scope.form = {}
      $scope.show_home_page = [
          {'name': 'show', 'value': '1'},
          {'name': 'hide', 'value': '0'},
      ];

      $('.link-button').on('click', function (e) {
          e.preventDefault();
          let cate_id = $(this).attr('id');

          $.ajax({
              type: 'GET',
              url: "/admin/service-categories/" + cate_id + "/getDataForEdit",
              headers: {
                  'X-CSRF-TOKEN': CSRF_TOKEN
              },
              success: function (response) {
                  if (response.success) {
                      $scope.form = response.data;
                      $scope.$applyAsync();
                  } else {
                  }
              },
              error: function (e) {
                  toastr.error('Đã có lỗi xảy ra');
              }
          });

          $("#create-origin").modal('show');
      })

      $scope.submit = function () {
          $scope.loading.submit = true;
          console.log($scope.form);
          $.ajax({
              type: 'POST',
              url: "{!! route('PostCategory.add.home.page') !!}",
              headers: {
                  'X-CSRF-TOKEN': CSRF_TOKEN
              },
              data:{
                  'cate_id' : $scope.form.id,
                  'show_home_page' : $scope.form.show_home_page,
                  'order_number' : $scope.form.order_number,
              },
              success: function (response) {
                  if (response.success) {
                      toastr.success(response.message);
                      location.reload();
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
      };

  });
</script>
@include('partial.confirm')
@endsection
