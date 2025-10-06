<div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        {{-- <div class="image">
            <img src="img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div> --}}
        <div class="info">
            @if(Auth::user()->type == App\Model\Common\User::SUPER_ADMIN)
            <a href="#" class="d-block" style="color: #fd7e14">Xin chào: Super Admin</a>
            @else
            <a href="#" class="d-block" style="color: #fd7e14">{{ App\Model\Common\User::find(Auth::user()->id)->name }}111</a>
            @endif
        </div>
    </div>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-legacy nav-flat" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
   with font-awesome or any other icon font library -->
            <li class="nav-item has-treeview menu-open">
                <a href="#" class="nav-link active">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>
                        Trang chủ
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
            </li>

            <li class="nav-item has-treeview  {{ request()->is('admin/categories') || request()->is('admin/categories*') ? 'menu-open' : '' }} ">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-stream"></i>
                    <p>
                        Danh mục hàng hóa
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('Category.index') }}" class="nav-link {{ Request::routeIs('Category.index') ? 'active' : '' }}">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>Danh sách danh mục</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('Category.create') }}"  class="nav-link {{ Request::routeIs('Category.create') ? 'active' : '' }}">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>Thêm mới danh mục</p>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item has-treeview  {{ request()->is('common/products') || request()->is('uptek/products*') ? 'menu-open' : '' }} ">
                <a href="#" class="nav-link">
                    <i class="nav-icon fab fa-dropbox"></i>
                    <p>
                        Hàng hóa
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('Product.index') }}"  class="nav-link {{ Request::routeIs('Product.create') ? 'active' : '' }}">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>Danh sách hàng hóa</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('Product.create') }}"  class="nav-link {{ Request::routeIs('Product.create') ? 'active' : '' }}">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>thêm mới hàng hóa</p>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item has-treeview  {{ request()->is('admin/post-categories') || request()->is('admin/post-categories*') ? 'menu-open' : '' }} ">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-stream"></i>
                    <p>
                        Danh mục bài viết
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('PostCategory.index') }}"  class="nav-link {{ Request::routeIs('PostCategory.create') ? 'active' : '' }}">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>Danh sách danh mục</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('PostCategory.create') }}" class="nav-link {{ Request::routeIs('PostCategory.create') ? 'active' : '' }}">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>Thêm mới danh mục</p>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item has-treeview  {{ request()->is('admin/posts') || request()->is('admin/posts*') ? 'menu-open' : '' }} ">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-newspaper"></i>
                    <p>
                        Bài viết
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('Post.index') }}" class="nav-link {{ Request::routeIs('Post.create') ? 'active' : '' }}">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>Danh sách bài viết</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('Post.create') }}" class="nav-link {{ Request::routeIs('Post.create') ? 'active' : '' }}">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>Thêm mới bài viết</p>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon far fa-handshake"></i>
                    <p>
                        Khách hàng
                        <i class=" fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('CustomerGroup.index') }}" class="nav-link">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>Nhóm khách</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('Customer.index') }}" class="nav-link">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>Danh sách khách</p>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item {{ Request::routeIs('Supplier.index') ? 'active' : '' }}">
                <a href="{{ route('Supplier.index') }}" class="nav-link">
                    <i class="fas fa-truck nav-icon"></i>
                    <p>
                        Nhà cung cấp
                    </p>
                </a>
            </li>
            <li class="nav-item has-treeview {{ request()->is('common/cars*') || request()->is('vehicle-manufacts*') || request()->is('vehicle-types*') || request()->is('vehicle-categories*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-th"></i>
                    <p>
                        Danh mục xe
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('VehicleManufact.index') }}" class="nav-link {{ Request::routeIs('VehicleManufact.index') ? 'active' : '' }}">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>Danh mục hãng xe</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('VehicleType.index') }}" class="nav-link {{ Request::routeIs('VehicleType.index') ? 'active' : '' }}">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>Danh mục loại xe</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('VehicleCategory.index') }}" class="nav-link {{ Request::routeIs('VehicleCategory.index') ? 'active' : '' }}">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>Danh mục dòng xe</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('Car.index') }}" class="nav-link {{ Request::routeIs('Car.index') ? 'active' : '' }}">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>Danh mục xe</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon fab fa-codepen"></i>
                    <p>
                        Tài sản cố định
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('FixedAsset.index') }}" class="nav-link">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>Danh sách tài sản</p>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-percent"></i>
                    <p>
                        CT Khuyến mãi
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('PromoCampaign.index') }}" class="nav-link">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>Danh sách chương trình</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('PromoCampaign.create') }}" class="nav-link">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>Thêm mới chương trình</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-chart-pie"></i>
                    <p>
                        Báo cáo thông kê
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>Báo cáo doanh thu</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>Báo cáo kho</p>
                        </a>
                    </li>
					<li class="nav-item">
						<a href="{{ route('Report.promoReport') }}" class="nav-link">
							<i class="far fas  fa-angle-right nav-icon"></i>
							<p>Báo cáo khuyến mại</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="{{ route('Report.promoProductReport') }}" class="nav-link">
							<i class="far fas  fa-angle-right nav-icon"></i>
							<p>Báo cáo khuyến mại tặng hàng</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="{{ route('Report.customerSaleReport') }}" class="nav-link">
							<i class="far fas  fa-angle-right nav-icon"></i>
							<p>Báo cáo bán hàng theo khách</p>
						</a>
					</li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>Báo cáo quỹ</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>Báo cáo khác</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon far fa-user"></i>
                    <p>
                        Người dùng
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('G7Info.index') }}" class="nav-link">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>Hồ sơ Gara G7</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('User.index') }}" class="nav-link">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>Tài khoản</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('User.create') }}" class="nav-link">
                            <i class="far fas fa-angle-right nav-icon"></i>
                            <p>Tạo tài khoản</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('Role.index') }}" class="nav-link">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>Chức vụ</p>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item has-treeview  {{ request()->is('uptek/configs') || request()->is('uptek/customer-levels') || request()->is('uptek/accumulate-point/*') ? 'menu-open' : '' }} ">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-cog"></i>
                    <p>
                        Cấu hình hệ thống
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('Config.edit') }}" class="nav-link  {{ Request::routeIs('Config.edit') ? 'active' : '' }}">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>Cấu hình chung</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('AccumulatePoint.edit') }}" class="nav-link  {{ Request::routeIs('AccumulatePoint.edit') ? 'active' : '' }}">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>Cấu hình điểm thường</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('CustomerLevel.index') }}" class="nav-link  {{ Request::routeIs('CustomerLevel.index') ? 'active' : '' }}">
                            <i class="far fas  fa-angle-right nav-icon"></i>
                            <p>Quản lý level khách hàng</p>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>
