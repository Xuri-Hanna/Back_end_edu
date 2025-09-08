<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HoaDonThuePhong;
use App\Models\PhieuThuePhong;
use App\Models\NguoiThuePhong;
use App\Models\HopDongThuePhong;
use Illuminate\Support\Facades\DB;

class ThongKeController extends Controller
{
    // THUÊ PHÒNG
    //Tổng thu nhập
     public function tongThuNhap(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');

        $tong = HoaDonThuePhong::whereBetween('ngay_lap', [$startDate, $endDate])
            ->sum('tong_tien');

    return response()->json([
        'tong_thu_nhap_raw' => $tong,
        'tong_thu_nhap_vn'  => number_format($tong, 0, ',', '.') . ' VNĐ'
    ]);

    }
    //Số phiếu thuê
     public function soPhieuThue(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');

        $count = PhieuThuePhong::whereDate('ngay_lap', '>=', $startDate)
              ->whereDate('ngay_lap', '<=', $endDate)
              ->count();

    return response()->json([
        'so_phieu_thue'=>$count
    ]);
    }
      public function soKhachMoi(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');

        $count = NguoiThuePhong::whereDate('created_at', '>=', $startDate)
              ->whereDate('created_at', '<=', $endDate)
              ->count();

    return response()->json([
        'so_khach_moi'=>$count
    ]);
    }
       public function soHopDong(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');

        $count = HopDongThuePhong::whereDate('ngay_lap', '>=', $startDate)
              ->whereDate('ngay_lap', '<=', $endDate)
              ->count();

    return response()->json([
        'so_hop_dong'=>$count
    ]);
    }
    public function topNhanVienHopDong(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');

        $data = DB::table('phieu_thue_phong')
            ->whereDate('ngay_lap', '>=', $startDate)
            ->whereDate('ngay_lap', '<=', $endDate)
            ->select('nhan_vien_id', DB::raw('count(*) as so_phieu_thue'))
            ->where('trang_thai', 'đã có hợp đồng')
            ->groupBy('nhan_vien_id')
            ->orderByDesc('so_phieu_thue')
            ->get();

        // Lấy thêm tên nhân viên
        $data = $data->map(function($item) {
            $nv = DB::table('nhan_vien')->where('id', $item->nhan_vien_id)->first();
            return [
                'id' => $item->nhan_vien_id,
                'ho_ten' => $nv->ho_ten ?? 'Không xác định',
                'so_phieu_thue' => $item->so_phieu_thue
            ];
        });

        return response()->json($data);
    }
    public function topPhong(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');

        $data = DB::table('phieu_thue_phong')
            ->whereDate('ngay_lap', '>=', $startDate)
            ->whereDate('ngay_lap', '<=', $endDate)
            ->select('phong_hoc_id', DB::raw('count(*) as so_phieu_thue'))
            ->where('trang_thai', 'đã có hợp đồng')
            ->groupBy('phong_hoc_id')
            ->orderByDesc('so_phieu_thue')
            ->get();

        // Lấy thêm số phòng
        $data = $data->map(function($item) {
            $ph = DB::table('phong_hoc')->where('id', $item->phong_hoc_id)->first();
            return [
                'id' => $item->phong_hoc_id,
                'so_phong' => $ph->so_phong ?? 'Không xác định',
                'so_phieu_thue' => $item->so_phieu_thue
            ];
        });

        return response()->json($data);
    }
    // GIÁO DỤC
    public function doanhThuHocPhi(Request $request){
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');

         $query = DB::table('hoa_don_hoc_phi')
            ->whereDate('ngay_lap', '>=', $startDate)
            ->whereDate('ngay_lap', '<=', $endDate)
            ->where('trang_thai','Đã thanh toán');
        $doanhthu = $query->sum('tong_tien');

         return response()->json([
            'doanh_thu_raw' => $doanhthu,
            'doanh_thu_vn'  => number_format($doanhthu, 0, ',', '.') . ' VNĐ'
        ]);
    }
    public function soHocSinh(){
        $query = DB::table('hoc_sinh')->count();

        return response()->json([
            'so_luong_hoc_sinh' => $query,
        ]);
    }
    public function lopHocStatus(Request $request)
    {
        // Thống kê số lớp theo trạng thái
        $data = DB::table('lop_hoc')
            ->select('trang_thai', DB::raw('count(*) as so_lop'))
            ->groupBy('trang_thai')
            ->get();

        // Chuyển về dạng key-value
        $result = [];
        foreach ($data as $item) {
            $result[$item->trang_thai] = $item->so_lop;
        }

        return response()->json($result);
    }
    public function topGiaoVien(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');

        // Join bảng lop_hoc với giao_vien để lấy tên
        $data = DB::table('lop_hoc as lh')
            ->whereDate('ngay_bat_dau', '>=', $startDate)
            ->whereDate('ngay_bat_dau', '<=', $endDate)
            ->join('giao_vien as gv', 'lh.giao_vien_id', '=', 'gv.id')
            ->select('gv.id', 'gv.ho_ten as ten', DB::raw('count(lh.id) as so_lop'))
            ->groupBy('gv.id', 'gv.ho_ten')
            ->orderByDesc('so_lop')
            ->get();

        return response()->json($data);
    }
     public function topMonHoc(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');

        // Join bảng lop_hoc với giao_vien để lấy tên
        $data = DB::table('lop_hoc as lh')
            ->whereDate('ngay_bat_dau', '>=', $startDate)
            ->whereDate('ngay_bat_dau', '<=', $endDate)
            ->join('mon_hoc as mh', 'lh.mon_hoc_id', '=', 'mh.id')
            ->select('mh.id', 'mh.mon_hoc as mon', DB::raw('count(lh.id) as so_lop'))
            ->groupBy('mh.id', 'mh.mon_hoc')
            ->orderByDesc('so_lop')
            ->get();

        return response()->json($data);
    }

    public function doanhThuHocPhiTheoThang(Request $request)
    {
        $year = $request->query('year', date('Y')); // Năm, mặc định là năm hiện tại

        $data = DB::table('hoa_don_hoc_phi')
            ->select(
                DB::raw('MONTH(ngay_lap) as thang'),
                DB::raw('SUM(tong_tien) as tong_doanh_thu')
            )
            ->whereYear('ngay_lap', $year)
            ->where('trang_thai', 'Đã thanh toán')
            ->groupBy(DB::raw('MONTH(ngay_lap)'))
            ->orderBy(DB::raw('MONTH(ngay_lap)'))
            ->get();

        // Chuẩn hóa: gán doanh thu = 0 cho những tháng không có dữ liệu
        $result = [];
        for ($i = 1; $i <= 12; $i++) {
            $doanhThu = $data->firstWhere('thang', $i)->tong_doanh_thu ?? 0;
            $result[] = [
                'thang' => 'Tháng ' . $i,
                'doanh_thu' => $doanhThu
            ];
        }

        return response()->json($result);
    }

    public function doanhThuThuePhongTheoThang(Request $request)
    {
        $year = $request->query('year', date('Y')); // mặc định năm hiện tại

        $data = DB::table('hoa_don_thue_phong')
            ->select(
                DB::raw('MONTH(ngay_lap) as thang'),
                DB::raw('SUM(tong_tien) as tong_doanh_thu')
            )
            ->whereYear('ngay_lap', $year)
            ->groupBy(DB::raw('MONTH(ngay_lap)'))
            ->orderBy(DB::raw('MONTH(ngay_lap)'))
            ->get();

        // Chuẩn hóa dữ liệu: gán 0 nếu tháng không có dữ liệu
        $result = [];
        for ($i = 1; $i <= 12; $i++) {
            $doanhThu = $data->firstWhere('thang', $i)->tong_doanh_thu ?? 0;
            $result[] = [
                'thang' => 'Tháng ' . $i,
                'doanh_thu' => $doanhThu
            ];
        }

        return response()->json($result);
    }


}

