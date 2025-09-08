<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\api\NhanVienController;
use App\Http\Controllers\api\GiaoVienController;
use App\Http\Controllers\api\HocSinhConTroller;
use App\Http\Controllers\api\TaiKhoanController;
use App\Http\Controllers\api\ChucVuController;
use App\Http\Controllers\api\DonViCongTacController;
use App\Http\Controllers\api\MonHocController;
use App\Http\Controllers\api\PhongHocController;
use App\Http\Controllers\api\NguoiThuePhongController;
use App\Http\Controllers\api\LichPhongController;
use App\Http\Controllers\api\LopHocController;
use App\Http\Controllers\api\ChiTietLopHocController;
use App\Http\Controllers\api\LichDayController;
use App\Http\Controllers\api\HoaDonHocPhiController;
use App\Http\Controllers\api\PhieuThuePhongController;
use App\Http\Controllers\api\HopDongThuePhongController;
use App\Http\Controllers\api\HoaDonThuePhongController;
use App\Http\Controllers\api\CongNoController;
use App\Http\Controllers\api\ThongKeController;



Route::middleware('api')->get('/user', function (Request $request) {
    return $request->user();
});

//LOGIN
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');

//HE THONG
Route::post('/change-password', [TaiKhoanController::class, 'changePassword']);
Route::get('/tai_khoans/search', [TaiKhoanController::class, 'search']);
Route::apiResource('tai_khoans',TaiKhoanConTroller::class);
Route::get('/get_tai_khoans', [TaiKhoanController::class, 'getUnused']);
Route::get('/chuc_vus/search', [ChucVuController::class, 'search']);
Route::apiResource('chuc_vus',ChucVuConTroller::class);
Route::get('/get_chuc_vus', [ChucVuController::class, 'getList']);
Route::get('/don_vi_cong_tacs/search', [DonViCongTacController::class, 'search']);
Route::apiResource('don_vi_cong_tacs', DonViCongTacController::class);
Route::get('/ten_don_vis', [DonViCongTacController::class, 'getTenDonVi']);


//NHAN SU
Route::get('/nhan_viens/search', [NhanVienController::class, 'search']);
Route::apiResource('nhan_viens',NhanVienController::class);
Route::get('/giao_viens/search', [GiaoVienController::class, 'search']);
Route::apiResource('giao_viens',GiaoVienController::class);
Route::get('/hoc_sinhs/search', [HocSinhController::class, 'search']);
Route::apiResource('hoc_sinhs',HocSinhConTroller::class);

//QUAN LY
Route::get('/phong_hocs/search', [PhongHocController::class, 'search']);
Route::apiResource('phong_hocs',PhongHocController::class);
Route::get('/mon_hocs/search', [MonHocController::class, 'search']);
Route::apiResource('mon_hocs',MonHocController::class);
Route::get('/nguoi_thue_phongs/search', [NguoiThuePhongController::class, 'search']);
Route::apiResource('nguoi_thue_phongs',NguoiThuePhongController::class);

//SAP LICH
Route::get('/lich_phongs',[LichPhongController::class,'index']);
Route::post('/lich_phong_updates', [LichPhongController::class, 'update']);
Route::post('/lich_phong_resets', [LichPhongController::class, 'reset']);
Route::get('/lop_hocs/{lopHoc}/lich_day', [LichDayController::class, 'index']);        // load lịch của lớp
Route::post('/lop_hocs/{lopHoc}/lich_day/toggle', [LichDayController::class, 'toggle']); // tick/untick ngay
Route::get('lop_hocs/giao_vien/{giao_vien_id}', [LopHocController::class, 'getLopHocByGiaoVien']);
Route::get('lich_day/dang_hocs', [LichDayController::class, 'lichDayDangHoc']);
Route::get('/lop_hocs_with_schedules', [LichDayController::class, 'indexWithSchedule']);
Route::get('/lich_day/giao_vien/{giao_vien_id}', [LichDayController::class, 'lichDayGiaoVien']);

//LOP_HOC
Route::apiResource('/lop_hocs',LopHocController::class);
Route::post('/chi_tiet_lops', [ChiTietLopHocController::class, 'store']);
Route::get('/lop_hocs/{lop_hoc_id}/hoc_sinhs', [ChiTietLopHocController::class, 'getHocSinhTheoLop']);
Route::delete('/lop_hocs/{lop_hoc_id}/hoc_sinhs/{hoc_sinh_id}', [ChiTietLopHocController::class, 'destroyByPair']);
Route::get('/lop_hocs/{lop_hoc_id}/hoc_sinhs/not_in', [ChiTietLopHocController::class, 'getHocSinhChuaThuocLop']);



//HOA DON
Route::apiResource('/hoa_don_hoc_phis',HoaDonHocPhiController::class);
Route::get('/hoc_sinh/{id}/lop-hoc', [ChiTietLopHocController::class, 'getLopHocByHocSinh']);
Route::get('/hoa_don/hoc_sinh/{id}', [HoaDonHocPhiController::class, 'getByHocSinh']);
Route::patch('/hoa_don_hoc_phis/{id}/trang_thai', [HoaDonHocPhiController::class, 'updateTrangThai']);

//THUE PHÒNG
Route::get('/phieu_thue_phongs/search', [PhieuThuePhongController::class, 'search']);
Route::apiResource('phieu_thue_phongs',PhieuThuePhongController::class);
Route::get('/hop_dong_thue_phongs', [HopDongThuePhongController::class, 'index']);
Route::patch('/hop_dong_thue_phongs/{id}/trang_thai', [HopDongThuePhongController::class, 'updateTrangThai']);
Route::get('/phieu_thue_phong/chua_co_hop_dongs', [PhieuThuePhongController::class, 'getChuaCoHopDong']);
Route::post('/hop_dong_thue_phongs', [HopDongThuePhongController::class, 'store']);
Route::get('/hop_dong_thue_phongs/by-phieu/{phieuId}', [HopDongThuePhongController::class, 'getByPhieu']);
Route::get('/kiem_tra_hop_dong/{phieuId}', [HopDongThuePhongController::class, 'kiemTraHopDong']);
Route::post('/hoa_don_thue_phongs', [HoaDonThuePhongController::class, 'store']);
Route::get('/hoa_don_thue_phong/hop_dong/{hopDongId}', [HoaDonThuePhongController::class, 'getByHopDong']);
Route::delete('/hoa_don/hop_dong/{hopDongId}', [HoaDonThuePhongController::class, 'deleteByHopDong']);
Route::get('/hop_dong/{id}/cong_no', [HopDongThuePhongController::class, 'congNo']);
Route::get('/cong_no/{id}', [CongNoController::class, 'show']);

//BÁO CÁO THỐNG KÊ
Route::get('/thong_ke/tong_thu_nhaps', [ThongKeController::class, 'tongThuNhap']);
Route::get('/thong_ke/phieu_thues', [ThongKeController::class, 'soPhieuThue']);
Route::get('/thong_ke/khach_mois', [ThongKeController::class, 'soKhachMoi']);
Route::get('/thong_ke/hop_dongs', [ThongKeController::class, 'soHopDong']);
Route::get('/thong_ke/hop_dong_status', [HopDongThuePhongController::class, 'thongKeTrangThai']);
Route::get('/thong_ke/top_nhan_vien_hop_dong', [ThongKeController::class, 'topNhanVienHopDong']);
Route::get('/thong_ke/top_phong', [ThongKeController::class, 'topPhong']);
Route::get('/thong_ke/doanh_thu_hoc_phi', [ThongKeController::class, 'doanhThuHocPhi']);
Route::get('/thong_ke/so_hoc_sinh', [ThongKeController::class, 'soHocSinh']);
Route::get('/thong_ke/lop_hoc_status', [ThongKeController::class, 'lopHocStatus']);
Route::get('/thong_ke/top_giao_vien', [ThongKeController::class, 'topGiaoVien']);
Route::get('/thong_ke/top_mon_hoc', [ThongKeController::class, 'topMonHoc']);

// BÁO CÁO DOANH THU THEO THÁNG
Route::get('/thong_ke/doanh_thu_hoc_phi_theo_thang', [ThongKeController::class, 'doanhThuHocPhiTheoThang']);
Route::get('/thong_ke/doanh_thu_thue_phong_theo_thang', [ThongKeController::class, 'doanhThuThuePhongTheoThang']);
