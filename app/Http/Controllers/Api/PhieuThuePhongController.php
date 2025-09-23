<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PhieuThuePhong;
use Illuminate\Support\Str;

class PhieuThuePhongController extends Controller
{
      public function index()
    {
        $phieuThue = PhieuThuePhong::with(['nhanVien', 'nguoiThuePhong', 'phongHoc','hopDongThuePhong'])->get();
        return response()->json($phieuThue);
    }


    public function search(Request $request)
    {
        $query = PhieuThuePhong::query();

        if ($request->keyword) {
            $keyword = $request->keyword;
            $query->where('id', 'like', "%$keyword%")
                ->orWhere('trang_thai', 'like', "%{$keyword}%")
                ->orWhereHas('nhanVien', fn($q) => $q->where('ho_ten', 'like', "%$keyword%"))
                ->orWhereHas('nguoiThuePhong', fn($q) => $q->where('ho_ten', 'like', "%$keyword%"))
                ->orWhereHas('phongHoc', fn($q) => $q->where('so_phong', 'like', "%$keyword%"));
        }

        if ($request->from_date && $request->to_date) {
            $query->whereBetween('tu_ngay', [$request->from_date, $request->to_date]);
        }
        if ($request->status) {
        $query->where('trang_thai', $request->status);
        }

        return response()->json($query->get());
    }


    // Lấy danh sách phiếu thuê chưa có hợp đồng
    public function getChuaCoHopDong()
    {
        $phieuThue = PhieuThuePhong::with(['nhanVien', 'nguoiThuePhong', 'phongHoc'])
            ->where('trang_thai', 'Chưa có hợp đồng')
            ->get();

        return response()->json($phieuThue);
    }
    // Lấy chi tiết 1 phiếu thuê phòng
    public function show($id)
    {
        $phieuThue = PhieuThuePhong::with(['nhanVien', 'nguoiThuePhong', 'phongHoc'])->findOrFail($id);
        return response()->json($phieuThue);
    }

    // Tạo mới phiếu thuê phòng
    public function store(Request $request)
    {
        $request->validate([
            'nhan_vien_id' => 'required|string',
            'nguoi_thue_phong_id' => 'required|string',
            'phong_hoc_id' => 'required|string',
            'tu_ngay' => 'required|date',
            'den_ngay' => 'required|date|after_or_equal:tu_ngay',
            'lich_thue' => 'nullable|string|max:50',
            'trang_thai' => 'required|in:Đã có hợp đồng,Chưa có hợp đồng',
            'ngay_lap'  =>  'nullable|date'
        ], [
            'den_ngay.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu',
            'tu_ngay.required' => 'Ngày bắt đầu là bắt buộc',
        ]);

       // Lấy ID cuối cùng trong DB
        $last = PhieuThuePhong::orderBy('id', 'desc')->first();

        if ($last) {
            // Lấy số phía sau PT
            $lastNumber = intval(substr($last->id, 2));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format ID: PT001, PT002...
        $newId = 'PT' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        $phieuThue = PhieuThuePhong::create([
            'id' => $newId,
            'nhan_vien_id' => $request->nhan_vien_id,
            'nguoi_thue_phong_id' => $request->nguoi_thue_phong_id,
            'phong_hoc_id' => $request->phong_hoc_id,
            'tu_ngay' => $request->tu_ngay,
            'den_ngay' => $request->den_ngay,
            'lich_thue' => $request->lich_thue,
            'trang_thai' => $request->trang_thai,
            'ngay_lap'  =>  $request->ngay_lap
        ]);

        return response()->json([
            'message' => 'Tạo phiếu thuê phòng thành công',
            'data' => $phieuThue
        ], 201);
    } 

    // Cập nhật phiếu thuê phòng
    public function update(Request $request, $id)
    {
        $request->validate([
            'nhan_vien_id' => 'required|string',
            'nguoi_thue_phong_id' => 'required|string',
            'phong_hoc_id' => 'required|string',
            'tu_ngay' => 'required|date',
            'den_ngay' => 'required|date|after_or_equal:tu_ngay',
            'lich_thue' => 'nullable|string|max:50',
            'ngay_lap'  =>  'nullable|date'
        ]);

        $phieuThue = PhieuThuePhong::findOrFail($id);

        $phieuThue->update([
            'nhan_vien_id' => $request->nhan_vien_id,
            'nguoi_thue_phong_id' => $request->nguoi_thue_phong_id,
            'phong_hoc_id' => $request->phong_hoc_id,
            'tu_ngay' => $request->tu_ngay,
            'den_ngay' => $request->den_ngay,
            'lich_thue' => $request->lich_thue,
        ]);

        return response()->json([
            'message' => 'Cập nhật phiếu thuê phòng thành công',
            'data' => $phieuThue
        ]);
    }

    // Xóa phiếu thuê phòng
    public function destroy($id)
    {
        // $phieuThue = PhieuThuePhong::findOrFail($id);
        // $phieuThue->delete();

        // return response()->json(['message' => 'Xóa phiếu thuê phòng thành công']);
        try {
        $phieuThue = PhieuThuePhong::findOrFail($id);
        $phieuThue->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa phiếu thuê phòng thành công!'
        ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể xóa phiếu thuê phòng vì còn dữ liệu liên quan!'
            ], 400);
        }
    }
}
