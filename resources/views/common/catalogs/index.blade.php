@extends('layouts.main')

@section('css')
@endsection

@section('title')
Quản lý dòng xe
@endsection
@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('style/nested.css') }}">
@endsection
@section('buttons')
<a href="{{ route('ProductCategory.create') }}" class="btn btn-outline-success" data-toggle="modal" href="javascript:void(0)" data-target="#create-product-category" class="btn btn-info" ng-click="errors = null"><i class="fa fa-plus"></i> Thêm mới</a>
<a href="{{ route('ProductCategory.exportExcel') }}" class="btn btn-primary"><i class="fas fa-file-excel"></i> Xuất file excel</a>
<a href="{{ route('ProductCategory.create') }}" class="btn btn-primary"><i class="far fa-file-pdf"></i> Xuất file pdf</a>
@endsection
@section('content')
<div ng-cloak>
    <div class="row" ng-controller="ProductCategory">
        <div class="col-12">
            <div class="card">
                <div class="nested-box">
                    <div class="col-lg-12 row-button">
                        <menu id="nestable-menu">
                            <button type="button" class="btn-primary" data-action="expand-all">Mở rộng <i class=" fa fa-plus"></i></button>
                            <button type="button" class="btn-danger" data-action="collapse-all">Thu gọn <i class="  fa fa-minus"></i></button>
                        </menu>
                        <input type="hidden" id="id">
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12-col-xs-12">
                        <div class="wrap bg-white">
                            <div class="title-block"><i class="fa fa-list"></i> Danh mục sản phẩm</div>
                            <div id="load"></div>
                            <div class="cf nestable-lists">
                                <div class="dd" id="nestable">
                                    <?php
                            $query = $catalog;
                            $ref   = [];
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
                                $html.= '<li class="dd-item dd3-item" data-id="'.$value['id'].'" >
                                <div class="dd-handle dd3-handle"></div>
                                <div class="dd3-content">  <span id="label_show'.$value['id'].'">'.$value['name'].'</span>
                                <span class="span-right">
                                <a class="link-button" href = "'. route('admin.catalog.edit',$value['slug']) .'" id="'.$value['id'].'" label="'.$value['name'].'" link="'.$value['slug'].'" ><i class="fa fa-link"></i></a>
                                <a class="edit-button" href = "'. route('admin.catalog.edit',$value['id']) .'" id="'.$value['id'].'" label="'.$value['name'].'" link="'.$value['slug'].'" ><i class="fas fa-pencil-alt"></i></a>
                                <a class="del-button" onclick="return confirmdelete('. "'" . 'Bạn có chắc sẽ xóa danh mục này?' . "'" .')" href = "'. route('admin.catalog.delete',$value['id']) .'" id="'.$value['id'].'"><i class="fa fa-trash"></i></a></span>
                                </div>';
                                if(array_key_exists('child',$value)) {
                                  $html .= get_menu($value['child'],'child');
                                }
                                $html .= "</li>";
                              }
                              $html .= "</ol>";

                              return $html;

                            }

                            print get_menu($items);

                            ?>
                                </div>
                            </div>
                            <p></p>
                            <input type="hidden" id="nestable-output">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12-col-xs-12">
                        <div class="wrap bg-white">
                            <div class="title-block"><i class="fa fa-list"></i> Thêm mới danh mục</div>
                            <form method="post" class="form-horizontal" role="form" action="{!! URL('admin/catalog/add') !!}" enctype="multipart/form-data">
                                @include('admin.blocks.error')
                                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                                <div class="form-group">
                                    <label for="sltParent" class="col-lg-3 col-sm-3 control-label"><strong>Danh mục cha</strong></label>
                                    <div class="col-lg-9">
                                        <select class="form-control" name="sltParent">
                                            <option value="0">Danh mục cha</option>
                                            <?php
                                cate_parent($catalog,0," |--",0);
                                ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="iptName" class="col-lg-3 control-label"><strong>Tên danh mục</strong></label>
                                    <div class="col-lg-9">
                                        <input class="form-control" name="iptName" value="{!! old('iptName') !!}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="iptCustomSlug" class="col-lg-3 col-sm-3 control-label">Đường dẫn<br /><span class="help-text" style="font-size: 10px; font-style: italic;">*Nếu không nhập sẽ lấy đường dẫn tự động</span></label>
                                    <div class="col-lg-9">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" id="customUrl" name="customUrl">
                                                Nhập đường dẫn (URL)
                                            </label>
                                        </div>
                                        <div style="float: left; margin-right: 7px; line-height: 32px; color: #777"><?php echo URL('/') ?>/</div><input style="max-width: 350px" type="text" class="form-control" name="iptCustomSlug" placeholder="" value="{!! old('iptCustomSlug') !!}">
                                    </div>
                                </div>
                                <!-- <div class="form-group">
                            <label for="iptImage" class="col-lg-3 col-sm-3 control-label"><strong>Ảnh đại diện</strong></label>
                            <div class="col-lg-9">
                              <input class="form-control" type="file" name="iptImage" value="{!! old('iptImage') !!}" >
                            </div>
                          </div> -->

                                <div class="form-group">
                                    <label for="" class="control-label col-lg-3 col-md-3"><strong>Hiện trang chủ </strong></label>
                                    <div class="col-lg-9 col-md-9">
                                        <div class="radio pull-left" style="margin-right: 15px;">
                                            <label>
                                                <input name="radioShowIndex" id="radioShowIndex" value="1" type="radio">
                                                có
                                            </label>
                                        </div>
                                        <div class="radio pull-left">
                                            <label>
                                                <input name="radioShowIndex" id="radioShowIndex" value="0" type="radio" checked>
                                                Không
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="form-group">
                            <label for="" class="col-lg-3 col-sm-3 control-label"><strong>Kiểu danh mục</strong></label>
                            <div class="col-md-9">
                              <div class="radio">
                                <label>
                                  <input type="radio" name="rdoType" id="input" value="0" checked="checked">
                                  Sản phẩm.
                                </label>
                                <label>
                                  <input type="radio" name="rdoType" id="input" value="1">
                                  Tin tức.
                                </label>
                                <label>
                                  <input type="radio" name="rdoType" id="input" value="2">
                                  Custom Menu.
                                </label>
                              </div>
                            </div>
                          </div> -->
                                <div class="form-group">
                                    <label for="iptKeywords" class="col-lg-3 control-label"><strong>Từ khóa SEO</strong></label>
                                    <div class="col-lg-9">
                                        <textarea name="txtKeywords" rows="3" class="form-control">{!! old('iptKeywords') !!}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="iptDes" class="col-lg-3 control-label"><strong>Mô tả SEO</strong></label>
                                    <div class="col-lg-9">
                                        <textarea name="txtDes" rows="5" class="form-control">{!! old('txtDes') !!}</textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-lg-12">
                                        <button type="submit" class="btn btn-success "><i class="fa fa-save"></i> Lưu</button>
                                        <button type="button" class="btn btn-default"><a href="{{ URL::previous() }}">Quay lại</a></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
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
      var baseURL = window.location.origin;
      var _token = $('input[name = _token]').val();

      $.ajax({
        type: "POST",
        url: baseURL + '/admin/catalog/nested-post',
        data: {dataString, _token},
        cache : false,
        success: function(data){
          $("#load").hide();
        } ,error: function(xhr, status, error) {
          alert(error);
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

</script>
@include('partial.confirm')
@endsection