<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LopHoc;
use App\Models\ChiTietLopHoc;
use App\Models\HocSinh;

class LopHocController extends Controller
{
    // Lấy danh sách tất cả lớp học
    public function index()
    {
        $data = LopHoc::with(['monHoc', 'giaoVien', 'phongHoc'])->get();
        return response()->json($data);
    }


    // Thêm lớp học
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_lop'       => 'required|string|max:255',
            'nam_hoc'       => 'required|string|max:255',
            'mon_hoc_id'    => 'required|string|exists:mon_hoc,id',
            'giao_vien_id'  => 'required|string|exists:giao_vien,id',
            'ngay_bat_dau'  => 'required|date',
            'ngay_ket_thuc' => 'required|date|after_or_equal:ngay_bat_dau',
            'so_luong'      => 'required|integer|min:1',
            'phong_hoc_id'  => 'required|string|exists:phong_hoc,id',
            'trang_thai'    => 'required|string|in:Sắp mở,Đang học,Đã hủy,Đã kết thúc',
            'don_gia'       => 'required|numeric|min:0',
            'so_buoi'       => 'required|integer|min:1',
        ]);

        // 👉 Lấy ID lớn nhất hiện tại
        $lastId = LopHoc::orderBy('id', 'desc')->value('id');

        if ($lastId) {
            $number = (int) substr($lastId, 2) + 1; // bỏ "LH", lấy số
        } else {
            $number = 1;
        }

        // 👉 Format thành LH001, LH002, ...
        $newId = 'LH' . str_pad($number, 3, '0', STR_PAD_LEFT);

        // 👉 Tạo lớp học mới
        $lopHoc = LopHoc::create(array_merge($validated, [
            'id' => $newId
        ]));

        return response()->json([
            'message' => 'Thêm lớp học thành công',
            'data' => $lopHoc
        ], 201);
    }
        public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $lopHoc = LopHoc::with(['monHoc', 'giaoVien', 'phongHoc'])
            ->when($keyword, function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('id', 'like', "%{$keyword}%")
                    ->orWhere('ten_lop', 'like', "%{$keyword}%")
                    ->orWhereHas('giaoVien', fn($q) => $q->where('ho_ten', 'like', "%$keyword%"))
                    ->orWhereHas('monHoc', fn($q) => $q->where('mon_hoc', 'like', "%$keyword%"))
                    ->orWhereHas('phongHoc', fn($q) => $q->where('so_phong', 'like', "%$keyword%"));
                });
            })
            ->get();

        return response()->json($lopHoc);
    }

    // Xem chi tiết 1 lớp học
    public function show($id)
    {
        $lopHoc = LopHoc::with(['monHoc', 'giaoVien', 'phongHoc'])->findOrFail($id);
        return response()->json($lopHoc);
    }

    // Cập nhật lớp học
    public function update(Request $request, $id)
    {
        $lopHoc = LopHoc::findOrFail($id);

        $validated = $request->validate([
            'ten_lop'       => 'sometimes|required|string|max:255',
            'nam_hoc'       => 'sometimes|required|string',
            'mon_hoc_id'    => 'sometimes|required|string|exists:mon_hoc,id',
            'giao_vien_id'  => 'sometimes|required|string|exists:giao_vien,id',
            'ngay_bat_dau'  => 'sometimes|required|date',
            'ngay_ket_thuc' => 'sometimes|required|date|after_or_equal:ngay_bat_dau',
            'so_luong'      => 'sometimes|required|integer|min:1',
            'phong_hoc_id'  => 'sometimes|required|string|exists:phong_hoc,id',
            'trang_thai'    => 'required|string|in:Sắp mở,Đang học,Đã hủy,Đã kết thúc',
            'don_gia'       => 'sometimes|required|numeric|min:0',
            'so_buoi'       => 'sometimes|required|integer|min:1',
        ]);

        $lopHoc->update($validated);

        return response()->json([
            'message' => 'Cập nhật lớp học thành công',
            'data' => $lopHoc
        ]);
    }

    // Xóa lớp học
    public function destroy($id)
    {
        $lopHoc = LopHoc::findOrFail($id);
        $lopHoc->delete();

        return response()->json([
            'message' => 'Xóa lớp học thành công'
        ]);
    }
    // Tìm kiếm lớp theo giáo viên và lấy lịch dạy
    public function getLopHocByGiaoVien($giao_vien_id)
    {

        $data = LopHoc::with(['monHoc', 'giaoVien', 'phongHoc'])
                        ->where('giao_vien_id',$giao_vien_id)
                        ->whereIn('trang_thai',['Đang học','Sắp mở'])
                        ->get();
        return response()->json($data);
    }

}
