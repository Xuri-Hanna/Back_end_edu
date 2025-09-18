<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChiTietLopHoc;
use App\Models\HocSinh;
use App\Models\LopHoc;

class ChiTietLopHocController extends Controller
{
    // Thêm học sinh vào lớp
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lop_hoc_id'  => 'required|exists:lop_hoc,id',
            'hoc_sinh_id' => 'required|exists:hoc_sinh,id',
        ]);

        // Kiểm tra học sinh đã có trong lớp chưa
        $exists = ChiTietLopHoc::where('lop_hoc_id', $validated['lop_hoc_id'])
            ->where('hoc_sinh_id', $validated['hoc_sinh_id'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Học sinh đã có trong lớp này'], 400);
        }

        $chiTiet = ChiTietLopHoc::create($validated);

        return response()->json($chiTiet, 201);
    }

    // Xem danh sách học sinh trong 1 lớp
    public function getHocSinhTheoLop($lop_hoc_id)
    {
        $hocSinhs = ChiTietLopHoc::with('hocSinh')
            ->where('lop_hoc_id', $lop_hoc_id)
            ->paginate(10);

        return response()->json([
            'data' => $hocSinhs->getCollection()->map->hocSinh,
            'current_page' => $hocSinhs->currentPage(),
            'last_page'    => $hocSinhs->lastPage(),
            'per_page'     => $hocSinhs->perPage(),
            'total'        => $hocSinhs->total(),
        ]);
    }

    // Xóa học sinh khỏi lớp
    // app/Http/Controllers/ChiTietLopHocController.php

    public function destroyByPair(string $lop_hoc_id, string $hoc_sinh_id)
    {
        $chiTiet = ChiTietLopHoc::where('lop_hoc_id', $lop_hoc_id)
            ->where('hoc_sinh_id', $hoc_sinh_id)
            ->first();

        if (!$chiTiet) {
            return response()->json([
                'message' => 'Không tìm thấy học sinh trong lớp theo cặp ID đã cung cấp',
            ], 404);
        }

        $chiTiet->delete();

        return response()->json([
            'message'    => 'Xóa học sinh khỏi lớp thành công',
            'deleted_id' => $chiTiet->id,
        ]);
    }
   public function getHocSinhChuaThuocLop($lop_hoc_id, Request $request)
    {
        // Lấy id học sinh đã thuộc lớp này
        $hocSinhTrongLop = ChiTietLopHoc::where('lop_hoc_id', $lop_hoc_id)
            ->pluck('hoc_sinh_id')
            ->toArray();

        // Bắt đầu query lấy học sinh chưa thuộc lớp
        $query = HocSinh::whereNotIn('id', $hocSinhTrongLop);

        // Nếu có từ khóa tìm kiếm
        if ($request->has('search') && !empty($request->search)) {
            $query->where('ho_ten', 'like', '%' . $request->search . '%');
        }
        // Lấy số bản ghi mỗi trang, mặc định = 10
        $perPage = (int) $request->get('per_page', 10);
        $hocSinhs = $query->paginate($perPage);

        return response()->json($hocSinhs);
    }
    public function getLopHocByHocSinh($hocSinhId)
    {
        $lopHocs = ChiTietLopHoc::join('lop_hoc', 'chi_tiet_lop_hoc.lop_hoc_id', '=', 'lop_hoc.id')
            ->where('chi_tiet_lop_hoc.hoc_sinh_id', $hocSinhId)
            ->where('lop_hoc.trang_thai', 'Đang học')
            ->select('lop_hoc.id', 'lop_hoc.ten_lop', 'lop_hoc.trang_thai')
            ->get();

        return response()->json($lopHocs);
    }
       public function getDSLopHocByHocSinh($hocSinhId)
    {
         $lopHocs = ChiTietLopHoc::join('lop_hoc', 'chi_tiet_lop_hoc.lop_hoc_id', '=', 'lop_hoc.id')
            ->where('chi_tiet_lop_hoc.hoc_sinh_id', $hocSinhId)
            ->whereIn('lop_hoc.trang_thai', ['Đang học', 'Sắp mở'])
            ->select('lop_hoc.id', 'lop_hoc.ten_lop', 'lop_hoc.trang_thai')
            ->get();

        return response()->json($lopHocs);
    }

}
