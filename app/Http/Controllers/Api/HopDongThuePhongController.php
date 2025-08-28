<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\PhieuThuePhong;
use Illuminate\Support\Str;
use App\Models\HopDongThuePhong;
use App\Models\CongNo;
class HopDongThuePhongController extends Controller
{
    public function index()
        {
            // Chỉ lấy id và trang_thai
            $hopDongs = HopDongThuePhong::select('id', 'trang_thai')->get();

            return response()->json([
                'data' => $hopDongs
            ]);
        }
   public function store(Request $request)
    {
        $request->validate([
            'phieu_thue_phong_id' => 'required|exists:phieu_thue_phong,id',
            'dieu_khoan' => 'required|string',
        ]);

        $phieu = PhieuThuePhong::with('phongHoc')->findOrFail($request->phieu_thue_phong_id);

        $tuNgay = Carbon::parse($phieu->tu_ngay);
        $denNgay = Carbon::parse($phieu->den_ngay);
        $soThang = $tuNgay->diffInMonths($denNgay);
        if ($soThang == 0) $soThang = 1;

        $giaPhong = $phieu->phongHoc->gia_phong;
        $thanhTien = $soThang * $giaPhong;

        // Tạo công nợ trước
        $congNo = CongNo::create([
            'tien_no' => $thanhTien,
            'da_tra' => 0,
        ]);

        // Sinh id hợp đồng thay vì lấy theo phiếu
        $last = HopDongThuePhong::orderBy('id', 'desc')->first();

        if ($last) {
            $lastNumber = intval(substr($last->id, 2));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $newId = 'HD' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        // Tạo hợp đồng
        $hopDong = HopDongThuePhong::create([
            'id' => $newId,
            'phieu_thue_phong_id' => $phieu->id,
            'dieu_khoan' => $request->dieu_khoan,
            'thanh_tien' => $thanhTien,
            'cong_no_id' => $congNo->id,
        ]);

        // Cập nhật trạng thái phiếu thuê
        $phieu->update(['trang_thai' => 'Đã có hợp đồng']);

        return response()->json([
            'message' => 'Tạo hợp đồng thành công',
            'data' => $hopDong->load('congNo', 'phieuThuePhong'),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $hopDong = HopDongThuePhong::with(['phieuThuePhong.phongHoc', 'phieuThuePhong.nhanVien', 'phieuThuePhong.nguoiThue'])
            ->findOrFail($id);

        return response()->json([
            'message' => 'Chi tiết hợp đồng',
            'data' => $hopDong
        ]);
    }
    public function getByPhieu($phieuId)
    {
         $phieuThue = PhieuThuePhong::with([
            'nhanVien.chucVu',
            'nguoiThuePhong',
            'phongHoc',
            'hopDongThuePhong' // cần có quan hệ này trong model
        ])->findOrFail($phieuId);

        // Format dữ liệu trả về
        $data = [
            'nguoi_thue' => [
                'ho_ten'     => $phieuThue->nguoiThuePhong->ho_ten ?? '',
                'dia_chi'    => $phieuThue->nguoiThuePhong->dia_chi ?? '',
                'dien_thoai' => $phieuThue->nguoiThuePhong->so_dien_thoai ?? '',
                'email'      => $phieuThue->nguoiThuePhong->email ?? '',
            ],
            'nhan_vien' => [
                'ho_ten'     => $phieuThue->nhanVien->ho_ten ?? '',
                'chuc_vu'    => $phieuThue->nhanVien->chucVu->ten ?? '',
                'dia_chi'    => $phieuThue->nhanVien->dia_chi ?? '',
                'dien_thoai' => $phieuThue->nhanVien->so_dien_thoai ?? '',
                'email'      => $phieuThue->nhanVien->email ?? '',
            ],
            'phong' => [
                'ten_phong'  => $phieuThue->phongHoc->so_phong ?? '',
                'so_cho'     => $phieuThue->phongHoc->so_cho_ngoi ?? '',
                'gia_phong'  => $phieuThue->phongHoc->gia_phong ?? '',
                'thoi_han'   => $phieuThue->tu_ngay . ' - ' . $phieuThue->den_ngay,
            ],
            'hop_dong' => [
                'dieu_khoan' => $phieuThue->hopDongThuePhong->dieu_khoan ?? '',
                'thanh_tien' => $phieuThue->hopDongThuePhong->thanh_tien ?? '',
                'trang_thai' => $phieuThue->hopDongThuePhong->trang_thai ?? '',
                'cong_no_id' => $phieuThue->hopDongThuePhong->cong_no_id ?? '',
            ]
        ];

        return response()->json($data);
    }
        public function kiemTraHopDong($phieuId)
    {
        $hopDong = HopDongThuePhong::where('phieu_thue_phong_id', $phieuId)->first();

        if (!$hopDong) {
            return response()->json(['message' => 'Không tìm thấy hợp đồng'], 404);
        }

        return response()->json($hopDong);
    }
    public function congNo($id)
    {
        // Tìm hợp đồng trước
        $hopDong = HopDongThuePhong::find($id);

        if (!$hopDong) {
            return response()->json(['message' => 'Không tìm thấy hợp đồng'], 404);
        }

        // Lấy công nợ theo cong_no_id trong bảng hợp đồng
        $congNo = CongNo::find($hopDong->cong_no_id);

        if (!$congNo) {
            return response()->json(['message' => 'Không tìm thấy công nợ'], 404);
        }

        return response()->json([
            'cong_no_id' => $congNo->id,
            'tien_no'    => $congNo->tien_no,
            'da_tra'     => $congNo->da_tra,
        ]);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
