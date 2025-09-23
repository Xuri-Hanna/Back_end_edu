<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HoaDonHocPhi;
use App\Models\LopHoc;
use Illuminate\Support\Str;
use Carbon\Carbon;

class HoaDonHocPhiController extends Controller
{
    // Lấy danh sách hóa đơn
    public function index()
    {
        $hoaDons = HoaDonHocPhi::with(['nhanVien', 'lopHoc'])->paginate(10);
        return response()->json($hoaDons);
    }

    // Lấy chi tiết 1 hóa đơn
    public function show($id)
    {
        $hoaDon = HoaDonHocPhi::with(['nhanVien', 'lopHoc'])->find($id);

        if (!$hoaDon) {
            return response()->json(['message' => 'Hóa đơn không tồn tại'], 404);
        }

        return response()->json($hoaDon);
    }

    private function generateRandomId($length = 10)
    {
        $id = '';
        for ($i = 0; $i < $length; $i++) {
            $id .= mt_rand(0, 9); // số ngẫu nhiên từ 0-9
        }
        return $id;
    }
    // Tạo hóa đơn mới
     public function store(Request $request)
    {
        $request->validate([
            'hoc_sinh_id' => 'required|exists:hoc_sinh,id',
            'lop_hoc_id' => 'required|exists:lop_hoc,id',
            'nhan_vien_id' => 'required|exists:nhan_vien,id',
            'ngay_het_han' => 'required|date|after:today',
        ]);

        $lopHoc = LopHoc::findOrFail($request->lop_hoc_id);

        $tongTien = $lopHoc->don_gia * $lopHoc->so_buoi;

        // Sinh id ngẫu nhiên 10 số
        $randomId = $this->generateRandomId(10);

        $hoaDon = HoaDonHocPhi::create([
            'id' => $randomId,
            'hoc_sinh_id' => $request->hoc_sinh_id,
            'nhan_vien_id' => $request->nhan_vien_id,
            'lop_hoc_id' => $lopHoc->id,
            'ngay_lap' => Carbon::now()->toDateString(), 
            'ngay_het_han' => $request->ngay_het_han,
            'tong_tien' => $tongTien,
            'trang_thai' => 'Chưa thanh toán'
        ]);

        return response()->json([
            'message' => 'Tạo hóa đơn thành công',
            'data' => $hoaDon,
        ], 201);
    }
    // Cập nhật hóa đơn
    public function update(Request $request, $id)
    {
        $hoaDon = HoaDonHocPhi::find($id);

        if (!$hoaDon) {
            return response()->json(['message' => 'Hóa đơn không tồn tại'], 404);
        }

        $validated = $request->validate([
            'nhan_vien_id' => 'sometimes|exists:nhan_vien,id',
            'lop_hoc_id'   => 'sometimes|exists:lop_hoc,id',
            'ngay_het_han' => 'sometimes|date',
            'ngay_lap'     => 'sometimes|date',
            'tong_tien'    => 'sometimes|numeric|min:0',
        ]);

        $hoaDon->update($validated);

        return response()->json([
            'message' => 'Cập nhật hóa đơn thành công',
            'data' => $hoaDon
        ]);
    }

    // Xóa hóa đơn
    public function destroy($id)
    {
        $hoaDon = HoaDonHocPhi::find($id);

        if (!$hoaDon) {
            return response()->json(['message' => 'Hóa đơn không tồn tại'], 404);
        }

        $hoaDon->delete();

        return response()->json(['message' => 'Xóa hóa đơn thành công']);
    }
      public function getByHocSinh($hocSinhId)
    {
        $hoaDons = HoaDonHocPhi::with(['hocSinh', 'lopHoc', 'nhanVien'])
            ->where('hoc_sinh_id', $hocSinhId)
            ->get();

        return response()->json($hoaDons);
    }
    public function updateTrangThai($id)
    {
        $hoaDon = HoaDonHocPhi::findOrFail($id);
        $hoaDon->trang_thai = 'Đã thanh toán';
        $hoaDon->save();

        return response()->json([
            'message' => 'Cập nhật trạng thái thành công',
            'hoa_don' => $hoaDon
        ]);
    }
}
