<?php
use Illuminate\Database\Seeder;
use App\Model\Uptek\PrintTemplate;

class PrintTemplatesTableSeeder extends Seeder
{
    public function run()
    {
		DB::table('print_templates')->truncate();
// Phiếu thu tiền
if (!PrintTemplate::find(PrintTemplate::PHIEU_THU_TIEN)) {
	PrintTemplate::create([
		'id' => PrintTemplate::PHIEU_THU_TIEN,
		'name' => 'Phiếu thu tiền',
		'code' =>'MAUIN-0001',
		'template' => '<table class="no-border" style="width:100%">
		<tbody>
			<tr>
				<td style="width:100%">{{invoice_header}}</td>
			</tr>
		</tbody>
	</table>

	<div> 
	<table class="no-border" style="table-layout:fixed; width:100%">
		<tbody>
			<tr>
				<td colspan="3" style="text-align:center"><span style="font-size:20px"><strong>PHIẾU THU TIỀN</strong></span><br />
				<em>No: {{SO_PHIEU}}</em></td>
			</tr>
			<tr>
				<td>Số phiếu: <strong>{{SO_PHIEU}}</strong></td>
				<td>Loại phiếu: <strong>{{LOAI_PHIEU}}</strong></td>
				<td>Ng&agrave;y ghi nhận:<strong>{{NGAY_GHI_NHAN}}</strong></td>
			</tr>
			<tr>
				<td>Người lập: <strong>{{NGUOI_LAP}}</strong></td>
				<td>Trạng th&aacute;i: <strong>{{TRANG_THAI}}</strong></td>
				<td>Đối tượng trả:<strong>{{DOI_TUONG_TRA}}</strong></td>
			</tr>
			<tr>
				<td>Người trả: <strong>{{NGUOI_TRA}}</strong></td>
				<td>Tổng gi&aacute; trị: <strong>{{TONG_GIA_TRI}}</strong></td>
				<td>Số h&oacute;a đơn mua:<strong>{{SO_HOA_DON}}</strong></td>
			</tr>
			<tr>
				<td colspan="3">Ghi ch&uacute;: <strong>{{GHI_CHU}}</strong></td>
			</tr>
		</tbody>
	</table>

	<div><br />
	<style type="text/css">td { border: 1px solid black; } table { border-collapse: collapse; }
	</style>
	</div>
	</div>
	',
		'created_by' => 1,
		'updated_by' => 1
	]);
}
// Hóa đơn bán (Bill)
		if (!PrintTemplate::find(PrintTemplate::HOA_DON_BAN)) {
			PrintTemplate::create([
				'id' => PrintTemplate::HOA_DON_BAN,
				'name' => 'Hóa đơn bán',
				'code' => 'MAUIN-0002',
				'template' => '<table class="no-border" style="width:100%">
				<tbody>
					<tr>
						<td style="width:100%">{{invoice_header}}</td>
					</tr>
				</tbody>
			</table>

			<div> 
			<table class="no-border" style="table-layout:fixed; width:100%">
				<tbody>
					<tr>
						<td colspan="3" style="text-align:center"><span style="font-size:20px"><strong>H&Oacute;A ĐƠN B&Aacute;N</strong></span><br />
						<em>No: {{SO_PHIEU}}</em></td>
					</tr>
					<tr>
						<td>Số phiếu: <strong>{{SO_PHIEU}}</strong></td>
						<td>Loại phiếu: <strong>{{LOAI_PHIEU}}</strong></td>
						<td>Ng&agrave;y ghi nhận:<strong>{{NGAY_GHI_NHAN}}</strong></td>
					</tr>
					<tr>
						<td>Người lập: <strong>{{NGUOI_LAP}}</strong></td>
						<td>Trạng th&aacute;i: <strong>{{TRANG_THAI}}</strong></td>
						<td>Đối tượng trả:<strong>{{DOI_TUONG_TRA}}</strong></td>
					</tr>
					<tr>
						<td>Người trả: <strong>{{NGUOI_TRA}}</strong></td>
						<td>Tổng gi&aacute; trị: <strong>{{TONG_GIA_TRI}}</strong></td>
						<td>Số h&oacute;a đơn mua:<strong>{{SO_HOA_DON}}</strong></td>
					</tr>
					<tr>
						<td colspan="3">Ghi ch&uacute;: <strong>{{GHI_CHU}}</strong></td>
					</tr>
				</tbody>
			</table>

			<div> 
			<div class="col-md-12 detail-info mb-3">{{CHI_TIET_HOA_DON}}</div>

			<div class="col-md-12 sumary-info">
			<h4><strong>TH&Ocirc;NG TIN THANH TO&Aacute;N</strong></h4>
			{{TONG_HOP}}</div>
			<style type="text/css">td {
							border: 1px solid black;
						}

						table {
							border-collapse: collapse;
						}
			</style>
			</div>
			</div>
			',
				'created_by' => 1,
				'updated_by' => 1
			]);
		}
// Hóa đơn bán dạng bill 57mm
		if (!PrintTemplate::find(PrintTemplate::HOA_DON_BAN_57)) {
			PrintTemplate::create([
				'id' => PrintTemplate::HOA_DON_BAN_57,
				'name' => 'Hóa đơn bán 57mm',
				'code' => 'MAUIN-0003',
				'template' => '<table class="no-border" style="width:100%">
				<tbody>
					<tr>
						<td style="width:100%">{{invoice_header}}</td>
					</tr>
				</tbody>
			</table>

			<table class="no-border" style="table-layout:fixed; width:100%">
				<tbody>
					<tr>
						<td colspan="3" style="text-align:center"><span style="font-size:14px"><strong>H&Oacute;A ĐƠN B&Aacute;N</strong></span><br />
						<em>No: {{SO_PHIEU}}</em></td>
					</tr>
				</tbody>
			</table>

			<table class="no-border" style="border:none; table-layout:fixed; width:100%">
				<tbody>
					<tr>
						<td>Số phiếu: <strong>{{SO_PHIEU}}</strong></td>
					</tr>
					<tr>
						<td>Ng&agrave;y HĐ: <strong>{{NGAY_GHI_NHAN}}</strong></td>
					</tr>
					<tr>
						<td>Kh&aacute;ch h&agrave;ng: <strong>{{KHACH_HANG}}</strong></td>
					</tr>
					<tr>
						<td>SĐT: <strong>{{DIEN_THOAI_KHACH}}</strong></td>
					</tr>
				</tbody>
			</table>

			<div class="col-md-12 detail-info mb-3">{{CHI_TIET_HOA_DON}}</div>

			<div class="col-md-12 sumary-info">
			<h4><strong style="font-size:14px">TH&Ocirc;NG TIN THANH TO&Aacute;N</strong></h4>
			{{TONG_HOP}}</div>
			<style type="text/css">td {
							border: 1px solid black;
						}

						table {
							border-collapse: collapse;
						}
			</style>
			',
				'type' => '57mm',
				'created_by' => 1,
				'updated_by' => 1
			]);
		}

// Báo cáo khuyến mãi
		if (!PrintTemplate::find(PrintTemplate::BAO_CAO_KHUYEN_MAI)) {
			PrintTemplate::create([
				'id' => PrintTemplate::BAO_CAO_KHUYEN_MAI,
				'name' => 'Báo cáo khuyến mại',
				'code' => 'MAUIN-0004',
				'template' => '<p style="text-align:center"><span style="font-size:18px"><strong>B&Aacute;O C&Aacute;O KHUYẾN MẠI</strong></span></p>

				<table border="0" cellpadding="0" cellspacing="0" class="no-border" style="width:100%">
					<tbody>
						<tr>
							<td style="width:50%"><strong>CTKM</strong>: {{CTKM}}</td>
							<td style="width:50%"> </td>
						</tr>
						<tr>
							<td style="width:50%"><strong>Từ ng&agrave;</strong>y: {{TU_NGAY}}</td>
							<td style="width:50%"><strong>Đến ng&agrave;y</strong>: {{DEN_NGAY}}</td>
						</tr>
						<tr>
							<td style="width:50%"><strong>Kh&aacute;ch h&agrave;ng</strong>: {{KHACH_HANG}}</td>
							<td style="width:50%"><strong>Gara</strong>: {{GARA}}</td>
						</tr>
					</tbody>
				</table>

				<div> </div>

				<div>{{CHI_TIET}}</div>',
				'created_by' => 1,
				'updated_by' => 1
			]);
		}
		// Báo cáo km theo hàng hóa
		if (!PrintTemplate::find(PrintTemplate::BAO_CAO_KHUYEN_MAI_HANG_HOA)) {
			PrintTemplate::create([
				'id' => PrintTemplate::BAO_CAO_KHUYEN_MAI_HANG_HOA,
				'name' => 'Báo cáo khuyến mại hàng hóa',
				'code' => 'MAUIN-0005',
				'template' => '<p style="text-align:center"><span style="font-size:18px"><strong>B&Aacute;O C&Aacute;O KHUYẾN MẠI H&Agrave;NG H&Oacute;A</strong></span></p>

				<table border="0" cellpadding="0" cellspacing="0" class="no-border" style="width:100%">
					<tbody>
						<tr>
							<td style="width:50%"><strong>CTKM</strong>: {{CTKM}}</td>
							<td style="width:50%"> </td>
						</tr>
						<tr>
							<td style="width:50%"><strong>Từ ng&agrave;</strong>y: {{TU_NGAY}}</td>
							<td style="width:50%"><strong>Đến ng&agrave;y</strong>: {{DEN_NGAY}}</td>
						</tr>
						<tr>
							<td style="width:50%"><strong>Kh&aacute;ch h&agrave;ng</strong>: {{KHACH_HANG}}</td>
							<td style="width:50%"><strong>Gara</strong>: {{GARA}}</td>
						</tr>
					</tbody>
				</table>

				<div> </div>

				<div>{{CHI_TIET}}</div>',
				'created_by' => 1,
				'updated_by' => 1
			]);
		}
// Phiếu nhập kho
		if (!PrintTemplate::find(PrintTemplate::PHIEU_NHAP_KHO)) {
			PrintTemplate::create([
				'id' => PrintTemplate::PHIEU_NHAP_KHO,
				'name' => 'Phiếu nhập kho',
				'code' => 'MAUIN-0006',
				'template' => '<table class="no-border" style="width:100%">
				<tbody>
					<tr>
						<td style="width:100%">{{invoice_header}}</td>
					</tr>
				</tbody>
			</table>

			<div> 
			<table class="no-border" style="table-layout:fixed; width:100%">
				<tbody>
					<tr>
						<td colspan="3" style="text-align:center"><span style="font-size:20px"><strong>PHIẾU NHẬP KHO</strong></span><br />
						<em>No: {{SO_PHIEU}}</em></td>
					</tr>
					<tr>
						<td>M&atilde; phiếu: <strong>{{SO_PHIEU}}</strong></td>
						<td>Ng&agrave;y ghi nhận: <strong>{{NGAY_GHI_NHAN}}</strong></td>
						<td>Người tạo:<strong>{{NGUOI_LAP}}</strong></td>
					</tr>
					<tr>
						<td>Trạng th&aacute;i: <strong>{{TRANG_THAI}}</strong></td>
						<td>Số lượng: <strong>{{TONG_SO_LUONG}}</strong></td>
						<td>Tổng gi&aacute; trj:<strong>{{TONG_GIA_TRI}}</strong></td>
					</tr>
					<tr>
						<td colspan="3">Nh&agrave; cung cấp: <strong>{{NHA_CUNG_CAP}}</strong></td>
					</tr>
					<tr>
						<td>Thanh to&aacute;n: <strong>{{THANH_TOAN}}</strong></td>
						<td colspan="">Ghi ch&uacute;:<strong>{{GHI_CHU}}</strong></td>
					</tr>
				</tbody>
			</table>

			<div> 
			<div class="col-md-12 detail-info mb-3">{{CHI_TIET_HOA_DON}}</div>

			<div class="col-md-12 sumary-info">
			<h4><strong>TH&Ocirc;NG TIN THANH TO&Aacute;N</strong></h4>
			{{TONG_HOP}}</div>
			<style type="text/css">td {
							border: 1px solid black;
						}

						table {
							border-collapse: collapse;
						}
			</style>
			</div>
			</div>
			',
				'created_by' => 1,
				'updated_by' => 1
			]);
		}
// Nhập TSCD
		if (!PrintTemplate::find(PrintTemplate::PHIEU_NHAP_TSCD)) {
			PrintTemplate::create([
				'id' => PrintTemplate::PHIEU_NHAP_TSCD,
				'name' => 'Phiếu nhập TSCD',
				'code' => 'MAUIN-0007',
				'template' => '<table class="no-border" style="width:100%">
				<tbody>
					<tr>
						<td style="width:100%">{{invoice_header}}</td>
					</tr>
				</tbody>
			</table>

			<div> 
			<table class="no-border" style="table-layout:fixed; width:100%">
				<tbody>
					<tr>
						<td colspan="3" style="text-align:center"><span style="font-size:20px"><strong>PHIẾU NHẬP KHO</strong></span><br />
						<em>No: {{SO_PHIEU}}</em></td>
					</tr>
					<tr>
						<td>M&atilde; phiếu: <strong>{{SO_PHIEU}}</strong></td>
						<td>Ng&agrave;y ghi nhận: <strong>{{NGAY_GHI_NHAN}}</strong></td>
						<td>Người tạo:<strong>{{NGUOI_LAP}}</strong></td>
					</tr>
					<tr>
						<td>Trạng th&aacute;i: <strong>{{TRANG_THAI}}</strong></td>
						<td>Số lượng: <strong>{{TONG_SO_LUONG}}</strong></td>
						<td>Tổng gi&aacute; trj:<strong>{{TONG_GIA_TRI}}</strong></td>
					</tr>
					<tr>
						<td colspan="3">Nh&agrave; cung cấp: <strong>{{NHA_CUNG_CAP}}</strong></td>
					</tr>
					<tr>
						<td>Thanh to&aacute;n: <strong>{{THANH_TOAN}}</strong></td>
						<td colspan="">Ghi ch&uacute;:<strong>{{GHI_CHU}}</strong></td>
					</tr>
				</tbody>
			</table>

			<div> 
			<div class="col-md-12 detail-info mb-3">{{CHI_TIET_HOA_DON}}</div>

			<div class="col-md-12 sumary-info">
			<h4><strong>TH&Ocirc;NG TIN THANH TO&Aacute;N</strong></h4>
			{{TONG_HOP}}</div>
			<style type="text/css">td {
							border: 1px solid black;
						}

						table {
							border-collapse: collapse;
						}
			</style>
			</div>
			</div>
			',
			'created_by' => 1,
			'updated_by' => 1
			]);
		}
// Báo cáo bán hàng theo khách
		if (!PrintTemplate::find(PrintTemplate::BAO_CAO_BAN_HANG_THEO_KHACH)) {
			PrintTemplate::create([
				'id' => PrintTemplate::BAO_CAO_BAN_HANG_THEO_KHACH,
				'name' => 'Báo cáo bán hàng theo khách',
				'code' => 'MAUIN-0008',
				'template' => '<p style="text-align:center"><span style="font-size:18px"><strong>B&Aacute;O C&Aacute;O BÁN HÀNG THEO KHÁCH</strong></span></p>

				<table border="0" cellpadding="0" cellspacing="0" class="no-border" style="width:100%">
					<tbody>
						<tr>
							<td style="width:50%"><strong>Từ ng&agrave;</strong>y: {{TU_NGAY}}</td>
							<td style="width:50%"><strong>Đến ng&agrave;y</strong>: {{DEN_NGAY}}</td>
						</tr>
						<tr>
							<td style="width:50%"><strong>Tỉnh</strong>: {{TINH}}</td>
							<td style="width:50%"><strong>Kh&aacute;ch h&agrave;ng</strong>: {{KHACH_HANG}}</td>
						</tr>
						<tr>
							<td style="width:50%"><strong>Gara</strong>: {{GARA}}</td>
							<td style="width:50%"> </td>
						</tr>
					</tbody>
				</table>

				<div> </div>

				<div>{{CHI_TIET}}</div>',
				'created_by' => 1,
				'updated_by' => 1
			]);
		}

		// Phiếu xuất kho

		if (!PrintTemplate::find(PrintTemplate::PHIEU_XUAT_KHO)) {
			PrintTemplate::create([
				'id' => PrintTemplate::PHIEU_XUAT_KHO,
				'name' => 'Phiếu xuất kho',
				'code' => 'MAUIN-0009',
				'template' => '<table class="no-border" style="width:100%">
				<tbody>
					<tr>
						<td style="width:100%">{{invoice_header}}</td>
					</tr>
				</tbody>
			</table>

			<div> 
			<table class="no-border" style="table-layout:fixed; width:100%">
				<tbody>
					<tr>
						<td colspan="3" style="text-align:center"><span style="font-size:20px"><strong>PHIẾU XUẤT KHO</strong></span><br />
						<em>No: {{SO_PHIEU}}</em></td>
					</tr>
					<tr>
						<td>M&atilde; phiếu: <strong>{{MA_PHIEU}}</strong></td>
						<td>Ng&agrave;y ghi nhận: <strong>{{NGAY_GHI_NHAN}}</strong></td>
						<td>Người tạo:<strong>{{NGUOI_LAP}}</strong></td>
					</tr>
					<tr>
						<td>Số h&oacute;a đơn b&aacute;n: <strong>{{SO_HOA_DON_BAN}}</strong></td>
						<td colspan="2">Số lượng: <strong>{{TONG_SO_LUONG}}</strong></td>
					</tr>
					<tr>
						<td colspan="3">Kh&aacute;ch h&agrave;ng: <strong>{{KHACH_HANG}}</strong></td>
					</tr>
				</tbody>
			</table>

			<div> 
			<div class="col-md-12 detail-info mb-3">{{CHI_TIET_HOA_DON}}</div>

			<div class="col-md-12 sumary-info">
			<h4><strong>TH&Ocirc;NG TIN THANH TO&Aacute;N</strong></h4>
			{{TONG_HOP}}</div>
			<style type="text/css">td {
									border: 1px solid black;
								}

								table {
									border-collapse: collapse;
								}
			</style>
			</div>
			</div>
			',
				'created_by' => 1,
				'updated_by' => 1
			]);
		}
    }
}
