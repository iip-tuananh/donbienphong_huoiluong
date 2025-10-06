<div class="row">
    <div class="col-sm-12">
        <div class="row">


            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Tên danh mục</label>
                    <input class="form-control " type="text" ng-model="form.name">
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.name[0] %></strong>
                    </span>
                </div>
            </div>


            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group custom-group">
                    <label class="form-label required-label">Hiển thị ngoài trang chủ</label>
                    <select id="my-select" class="form-control custom-select" ng-model="form.show_home_page">
                        <option ng-repeat="s in show_home_page" ng-value="s.value" ng-selected="form.show_home_page == s.value"><% s.name %></option>

                    </select>
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><% errors.show_home_page[0] %></strong>
                    </span>
                </div>
            </div>


{{--            <div class="col-md-12 col-sm-12 col-xs-12">--}}
{{--                <div class="form-group custom-group">--}}
{{--                    <label class="form-label">Vị trí</label>--}}
{{--                    <input class="form-control " type="number" ng-model="form.order_number">--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>
    </div>
</div>
