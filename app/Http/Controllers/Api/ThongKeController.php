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
}

