<?php
use Illuminate\Database\Seeder;
use App\Model\Common\Permission;
use App\Model\Common\User;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        Cache::flush('spatie.permission.cache');
        Cache::flush('spatie.role.cache');

        Permission::createRecord(['id' => 1, 'name' => 'Thêm dịch vụ', 'display_name' => 'Tạo mới', 'group' => 'Quản lý dịch vụ'], [User::QUAN_TRI_VIEN]);
        Permission::createRecord(['id' => 2, 'name' => 'Sửa dịch vụ', 'display_name' => 'Sửa', 'group' => 'Quản lý dịch vụ'], [User::QUAN_TRI_VIEN]);
        Permission::createRecord(['id' => 3, 'name' => 'Xóa dịch vụ', 'display_name' => 'Xóa', 'group' => 'Quản lý dịch vụ'], [User::QUAN_TRI_VIEN]);

		Permission::createRecord(['id' => 4, 'name' => 'Thêm hàng hóa', 'display_name' => 'Tạo mới', 'group' => 'Quản lý hàng hóa'], [User::QUAN_TRI_VIEN]);
        Permission::createRecord(['id' => 5, 'name' => 'Sửa hàng hóa', 'display_name' => 'Sửa', 'group' => 'Quản lý hàng hóa'], [User::QUAN_TRI_VIEN]);
        Permission::createRecord(['id' => 6, 'name' => 'Xóa hàng hóa', 'display_name' => 'Xóa', 'group' => 'Quản lý hàng hóa'], [User::QUAN_TRI_VIEN]);
        Permission::createRecord(['id' => 95, 'name' => 'Cập nhật giá hàng hóa', 'display_name' => 'Cập nhật giá hàng hóa', 'group' => 'Quản lý hàng hóa'], [User::G7, User::NHAN_VIEN_G7]);

		Permission::createRecord(['id' => 7, 'name' => 'Cấu hình tích điểm', 'display_name' => 'Tích điểm', 'group' => 'Cấu hình'], [User::QUAN_TRI_VIEN]);
        Permission::createRecord(['id' => 8, 'name' => 'Cấu hình level khách hàng', 'display_name' => 'Level khách hàng', 'group' => 'Cấu hình'], [User::QUAN_TRI_VIEN]);

		Permission::createRecord(['id' => 9, 'name' => 'Thêm bài viết', 'display_name' => 'Tạo mới', 'group' => 'Quản lý bài viết'], [User::QUAN_TRI_VIEN]);
        Permission::createRecord(['id' => 10, 'name' => 'Sửa bài viết', 'display_name' => 'Sửa', 'group' => 'Quản lý bài viết'], [User::QUAN_TRI_VIEN]);
        Permission::createRecord(['id' => 11, 'name' => 'Xóa bài viết', 'display_name' => 'Xóa', 'group' => 'Quản lý bài viết'], [User::QUAN_TRI_VIEN]);

		Permission::createRecord(['id' => 12, 'name' => 'Cấu hình mẫu in', 'display_name' => 'Mẫu in', 'group' => 'Cấu hình'], [User::QUAN_TRI_VIEN]);

		Permission::createRecord(['id' => 13, 'name' => 'Thêm tài sản cố định', 'display_name' => 'Tạo mới', 'group' => 'Quản lý tài sản cố định'], [User::QUAN_TRI_VIEN, User::G7, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 14, 'name' => 'Sửa tài sản cố định', 'display_name' => 'Sửa', 'group' => 'Quản lý tài sản cố định'], [User::QUAN_TRI_VIEN, User::G7, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 15, 'name' => 'Xóa tài sản cố định', 'display_name' => 'Xóa', 'group' => 'Quản lý tài sản cố định'], [User::QUAN_TRI_VIEN, User::G7, User::NHAN_VIEN_G7]);

        Permission::createRecord(['id' => 61, 'name' => 'Tạo phiếu nhập TSCD', 'display_name' => 'Tạo phiếu nhập TSCD', 'group' => 'Quản lý tài sản cố định'], [User::G7, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 62, 'name' => 'Cập nhật phiếu nhập TSCD', 'display_name' => 'Cập nhật phiếu nhập TSCD', 'group' => 'Quản lý tài sản cố định'], [User::G7, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 63, 'name' => 'Hủy phiếu nhập TSCD', 'display_name' => 'Hủy phiếu nhập TSCD', 'group' => 'Quản lý tài sản cố định'], [User::G7, User::NHAN_VIEN_G7]);

		Permission::createRecord(['id' => 16, 'name' => 'Thêm điểm G7', 'display_name' => 'Tạo mới', 'group' => 'Quản lý điểm G7'], [User::QUAN_TRI_VIEN]);
        Permission::createRecord(['id' => 17, 'name' => 'Sửa điểm G7', 'display_name' => 'Sửa', 'group' => 'Quản lý điểm G7'], [User::QUAN_TRI_VIEN]);
        Permission::createRecord(['id' => 18, 'name' => 'Xóa điểm G7', 'display_name' => 'Xóa', 'group' => 'Quản lý điểm G7'], [User::QUAN_TRI_VIEN]);

		Permission::createRecord(['id' => 19, 'name' => 'Thêm loại hàng hóa', 'display_name' => 'Tạo mới', 'group' => 'Quản lý loại hàng hóa'], [User::QUAN_TRI_VIEN]);
        Permission::createRecord(['id' => 20, 'name' => 'Sửa loại hàng hóa', 'display_name' => 'Sửa', 'group' => 'Quản lý loại hàng hóa'], [User::QUAN_TRI_VIEN]);
        Permission::createRecord(['id' => 21, 'name' => 'Xóa loại hàng hóa', 'display_name' => 'Xóa', 'group' => 'Quản lý loại hàng hóa'], [User::QUAN_TRI_VIEN]);

		Permission::createRecord(['id' => 22, 'name' => 'Thêm loại dịch vụ', 'display_name' => 'Tạo mới', 'group' => 'Quản lý loại dịch vụ'], [User::QUAN_TRI_VIEN]);
        Permission::createRecord(['id' => 23, 'name' => 'Sửa loại dịch vụ', 'display_name' => 'Sửa', 'group' => 'Quản lý loại dịch vụ'], [User::QUAN_TRI_VIEN]);
        Permission::createRecord(['id' => 24, 'name' => 'Xóa loại dịch vụ', 'display_name' => 'Xóa', 'group' => 'Quản lý loại dịch vụ'], [User::QUAN_TRI_VIEN]);

		Permission::createRecord(['id' => 25, 'name' => 'Cấu hình chung', 'display_name' => 'Cấu hình chung', 'group' => 'Cấu hình'], [User::QUAN_TRI_VIEN]);

		Permission::createRecord(['id' => 26, 'name' => 'Thêm hồ sơ nhân viên', 'display_name' => 'Tạo mới', 'group' => 'Quản lý hồ sơ nhân viên'], [User::G7, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 27, 'name' => 'Sửa hồ sơ nhân viên', 'display_name' => 'Sửa', 'group' => 'Quản lý hồ sơ nhân viên'], [User::G7, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 28, 'name' => 'Xóa hồ sơ nhân viên', 'display_name' => 'Xóa', 'group' => 'Quản lý hồ sơ nhân viên'], [User::G7, User::NHAN_VIEN_G7]);

		Permission::createRecord(['id' => 29, 'name' => 'Thêm nhắc lịch', 'display_name' => 'Tạo mới', 'group' => 'Quản lý nhắc lịch'], [User::G7, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 30, 'name' => 'Sửa nhắc lịch', 'display_name' => 'Sửa', 'group' => 'Quản lý nhắc lịch'], [User::G7, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 31, 'name' => 'Xóa nhắc lịch', 'display_name' => 'Xóa', 'group' => 'Quản lý nhắc lịch'], [User::G7, User::NHAN_VIEN_G7]);

        Permission::createRecord(['id' => 32, 'name' => 'Quản lý chức vụ', 'display_name' => 'Quản lý chung', 'group' => 'Quản lý chức vụ'], [User::G7, User::QUAN_TRI_VIEN]);

        Permission::createRecord(['id' => 33, 'name' => 'Quản lý người dùng', 'display_name' => 'Quản lý chung', 'group' => 'Quản lý người dùng'], [User::G7, User::QUAN_TRI_VIEN]);

        Permission::createRecord(['id' => 34, 'name' => 'Quản lý chương trình khuyến mãi', 'display_name' => 'Quản lý chung', 'group' => 'Quản lý khuyến mãi'], [User::G7, User::QUAN_TRI_VIEN, User::NHAN_VIEN_G7]);

        Permission::createRecord(['id' => 43, 'name' => 'Thêm mới đơn vị', 'display_name' => 'Tạo mới', 'group' => 'Quản lý đơn vị tính'], [User::QUAN_TRI_VIEN]);
        Permission::createRecord(['id' => 44, 'name' => 'Cập nhật đơn vị', 'display_name' => 'Sửa', 'group' => 'Quản lý đơn vị tính'], [User::QUAN_TRI_VIEN]);
        Permission::createRecord(['id' => 45, 'name' => 'Xóa đơn vị tính', 'display_name' => 'Xóa', 'group' => 'Quản lý đơn vị tính'], [User::QUAN_TRI_VIEN]);

        Permission::createRecord(['id' => 46, 'name' => 'Thêm mới biển số xe', 'display_name' => 'Tạo mới', 'group' => 'Quản lý biển số xe'], [User::QUAN_TRI_VIEN, User::G7, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 47, 'name' => 'Cập nhật biển số xe', 'display_name' => 'Sửa', 'group' => 'Quản lý biển số xe'], [User::QUAN_TRI_VIEN, User::G7, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 48, 'name' => 'Xóa biển số xe', 'display_name' => 'Xóa', 'group' => 'Quản lý biển số xe'], [User::QUAN_TRI_VIEN, User::G7, User::NHAN_VIEN_G7]);

        Permission::createRecord(['id' => 49, 'name' => 'Thêm mới xe', 'display_name' => 'Tạo mới', 'group' => 'Quản lý xe'], [User::QUAN_TRI_VIEN, User::G7, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 50, 'name' => 'Cập nhật xe', 'display_name' => 'Sửa', 'group' => 'Quản lý xe'], [User::QUAN_TRI_VIEN, User::G7, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 51, 'name' => 'Xóa xe', 'display_name' => 'Xóa', 'group' => 'Quản lý xe'], [User::QUAN_TRI_VIEN, User::G7, User::NHAN_VIEN_G7]);

        Permission::createRecord(['id' => 52, 'name' => 'Thêm mới khách hàng', 'display_name' => 'Tạo mới', 'group' => 'Quản khách hàng'], [User::QUAN_TRI_VIEN, User::G7, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 53, 'name' => 'Cập nhật khách hàng', 'display_name' => 'Sửa', 'group' => 'Quản khách hàng'], [User::QUAN_TRI_VIEN, User::G7, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 54, 'name' => 'Xóa khách hàng', 'display_name' => 'Xóa', 'group' => 'Quản khách hàng'], [User::QUAN_TRI_VIEN, User::G7, User::NHAN_VIEN_G7]);

        Permission::createRecord(['id' => 55, 'name' => 'Cập nhật cấu hình', 'display_name' => 'Cập nhật', 'group' => 'Quản lý cấu hình chung'], [User::QUAN_TRI_VIEN]);

        Permission::createRecord(['id' => 58, 'name' => 'Thêm mới tài khoản nhân viên G7', 'display_name' => 'Tạo mới', 'group' => 'Quản lý tài khoản nhân viên G7'], [User::G7]);
        Permission::createRecord(['id' => 59, 'name' => 'Cập nhật tài khoản nhân viên G7', 'display_name' => 'Sửa', 'group' => 'Quản lý tài khoản nhân viên G7'], [User::G7]);
        Permission::createRecord(['id' => 60, 'name' => 'Xóa tài khoản nhân viên G7', 'display_name' => 'Xóa', 'group' => 'Quản lý tài khoản nhân viên G7'], [User::G7]);



        Permission::createRecord(['id' => 64, 'name' => 'Tạo nhà cung cấp', 'display_name' => 'Tạo mới', 'group' => 'Quản lý nhà cung cấp'], [User::G7, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 65, 'name' => 'Cập nhật nhà cung cấp', 'display_name' => 'Sửa', 'group' => 'Quản lý nhà cung cấp'], [User::G7, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 66, 'name' => 'Xóa nhà cung cấp', 'display_name' => 'Xóa', 'group' => 'Quản lý nhà cung cấp'], [User::G7, User::NHAN_VIEN_G7]);

        Permission::createRecord(['id' => 67, 'name' => 'Tạo phiếu thu', 'display_name' => 'Tạo phiếu thu', 'group' => 'Quản lý quỹ'], [User::G7, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 68, 'name' => 'Tạo phiếu chi', 'display_name' => 'Tạo phiếu chi', 'group' => 'Quản lý quỹ'], [User::G7, User::NHAN_VIEN_G7]);

        Permission::createRecord(['id' => 69, 'name' => 'Tạo phiếu nhập kho', 'display_name' => 'Tạo phiếu nhập kho', 'group' => 'Quản lý kho'], [User::G7, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 70, 'name' => 'Cập nhật phiếu nhập kho', 'display_name' => 'Cập nhật phiếu nhập kho', 'group' => 'Quản lý kho'], [User::G7, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 71, 'name' => 'Tạo phiếu xuất kho', 'display_name' => 'Tạo phiếu xuất kho', 'group' => 'Quản lý kho'], [User::G7, User::NHAN_VIEN_G7]);

        Permission::createRecord(['id' => 72, 'name' => 'Tạo hóa đơn bán hàng', 'display_name' => 'Tạo hóa đơn', 'group' => 'Quản lý bán hàng'], [User::G7, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 73, 'name' => 'Cập nhật hóa đơn bán hàng', 'display_name' => 'Cập nhật hóa đơn', 'group' => 'Quản lý bán hàng'], [User::G7, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 74, 'name' => 'Xem hóa đơn bán', 'display_name' => 'Xem hóa đơn', 'group' => 'Quản lý bán hàng'], [User::G7, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 93, 'name' => 'Hủy hóa đơn bán', 'display_name' => 'Hủy hóa đơn', 'group' => 'Quản lý bán hàng'], [User::G7, User::NHAN_VIEN_G7]);

        Permission::createRecord(['id' => 75, 'name' => 'Thêm mới hãng xe', 'display_name' => 'Tạo mới', 'group' => 'Quản lý hãng xe'], [User::QUAN_TRI_VIEN]);
        Permission::createRecord(['id' => 76, 'name' => 'Cập nhật hãng xe', 'display_name' => 'Sửa', 'group' => 'Quản lý hãng xe'], [User::QUAN_TRI_VIEN]);
        Permission::createRecord(['id' => 77, 'name' => 'Xóa hãng xe', 'display_name' => 'Xóa', 'group' => 'Quản lý hãng xe'], [User::QUAN_TRI_VIEN]);

        Permission::createRecord(['id' => 78, 'name' => 'Thêm mới loại xe', 'display_name' => 'Tạo mới', 'group' => 'Quản lý loại xe'], [User::QUAN_TRI_VIEN]);
        Permission::createRecord(['id' => 79, 'name' => 'Cập nhật loại xe', 'display_name' => 'Sửa', 'group' => 'Quản lý loại xe'], [User::QUAN_TRI_VIEN]);
        Permission::createRecord(['id' => 80, 'name' => 'Xóa loại xe', 'display_name' => 'Xóa', 'group' => 'Quản lý loại xe'], [User::QUAN_TRI_VIEN]);

        Permission::createRecord(['id' => 81, 'name' => 'Thêm mới dòng xe', 'display_name' => 'Tạo mới', 'group' => 'Quản lý dòng xe'], [User::QUAN_TRI_VIEN]);
        Permission::createRecord(['id' => 82, 'name' => 'Cập nhật dòng xe', 'display_name' => 'Sửa', 'group' => 'Quản lý dòng xe'], [User::QUAN_TRI_VIEN]);
        Permission::createRecord(['id' => 83, 'name' => 'Xóa dòng xe', 'display_name' => 'Xóa', 'group' => 'Quản lý dòng xe'], [User::QUAN_TRI_VIEN]);

        Permission::createRecord(['id' => 84, 'name' => 'Thêm mới nhóm khách hàng', 'display_name' => 'Tạo mới', 'group' => 'Quản lý nhóm khách hàng'], [User::QUAN_TRI_VIEN]);
        Permission::createRecord(['id' => 85, 'name' => 'Cập nhật nhóm khách hàng', 'display_name' => 'Sửa', 'group' => 'Quản lý nhóm khách hàng'], [User::QUAN_TRI_VIEN]);
        Permission::createRecord(['id' => 86, 'name' => 'Xóa nhóm khách hàng', 'display_name' => 'Xóa', 'group' => 'Quản lý nhóm khách hàng'], [User::QUAN_TRI_VIEN]);
// Báo cáo
        Permission::createRecord(['id' => 87, 'name' => 'Xem báo cáo kinh doanh theo khách hàng', 'display_name' => 'Xem báo cáo kinh doanh theo khách hàng', 'group' => 'Quản lý báo cáo thống kê'], [User::G7, User::NHAN_VIEN_G7, User::QUAN_TRI_VIEN]);
        Permission::createRecord(['id' => 88, 'name' => 'Xem báo cáo kho', 'display_name' => 'Xem báo cáo kho', 'group' => 'Quản lý báo cáo thống kê'], [User::G7, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 89, 'name' => 'Xem báo cáo bán hàng', 'display_name' => 'Xem báo cáo bán hàng', 'group' => 'Quản lý báo cáo thống kê'], [User::G7, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 90, 'name' => 'Xem báo cáo quỹ', 'display_name' => 'Xem báo cáo quỹ', 'group' => 'Quản lý báo cáo thống kê'], [User::G7, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 91, 'name' => 'Xem báo cáo khuyến mãi chiết khấu', 'display_name' => 'Xem báo cáo khuyến mãi chiết khấu', 'group' => 'Quản lý báo cáo thống kê'], [User::G7, User::QUAN_TRI_VIEN, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 92, 'name' => 'Xem báo cáo khuyến mãi theo hàng hóa', 'display_name' => 'Xem báo cáo khuyến mãi theo hàng hóa', 'group' => 'Quản lý báo cáo thống kê'], [User::G7, User::QUAN_TRI_VIEN, User::NHAN_VIEN_G7]);
        Permission::createRecord(['id' => 94, 'name' => 'Xem báo cáo tài sản cố định', 'display_name' => 'Xem báo cáo tài sản cố định', 'group' => 'Quản lý báo cáo thống kê'], [User::G7, User::NHAN_VIEN_G7]);
	}
}
