<?php

namespace App\Http\Controllers\Api;
use App\Models\HocSinh;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HocSinhController extends Controller
{
    // Lấy danh sách học sinh
    public function index()
    {
        return response()->json(HocSinh::all(), 200);
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $hocSinhs = HocSinh::when($keyword, function ($query, $keyword) {
            $query->where('id', 'like', "%{$keyword}%")
                ->orWhere('ho_ten', 'like', "%{$keyword}%");
        })->get();

        return response()->json($hocSinhs, 200);
    }

    // Thêm học sinh mới
    public function store(Request $request)
    {
        $request->validate([
            'ho_ten' => 'required|string|max:255',
            'so_dien_thoai' => 'nullable|string|max:15',
            'gioi_tinh' => 'nullable|string|max:10',
            'ngay_sinh' => 'nullable|date',
            'dia_chi' => 'nullable|string|max:255',
            'so_phu_huynh' => 'nullable|string|max:15',
        ]);

        // Lấy id mới
        $lastHS = HocSinh::orderBy('id', 'desc')->first();
        if ($lastHS) {
            $lastNumber = (int) substr($lastHS->id, 2);
            $newId = 'HS' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newId = 'HS001';
        }

        $hocSinh = HocSinh::create([
            'id' => $newId,
            'ho_ten' => $request->ho_ten,
            'so_dien_thoai' => $request->so_dien_thoai,
            'gioi_tinh' => $request->gioi_tinh,
            'ngay_sinh' => $request->ngay_sinh,
            'dia_chi' => $request->dia_chi,
            'so_phu_huynh' => $request->so_phu_huynh,
        ]);

        return response()->json($hocSinh, 201);
    }

    // Lấy chi tiết học sinh
    public function show($id)
    {
        $hocSinh = HocSinh::find($id);
        if (!$hocSinh) {
            return response()->json(['message' => 'Không tìm thấy học sinh'], 404);
        }
        return response()->json($hocSinh, 200);
    }

    // Cập nhật học sinh
    public function update(Request $request, $id)
    {
        $hocSinh = HocSinh::find($id);
        if (!$hocSinh) {
            return response()->json(['message' => 'Không tìm thấy học sinh'], 404);
        }

        $hocSinh->update($request->all());
        return response()->json($hocSinh, 200);
    }

    // Xóa học sinh
    public function destroy($id)
    {
    //     $hocSinh = HocSinh::find($id);
    //     if (!$hocSinh) {
    //         return response()->json(['message' => 'Không tìm thấy học sinh'], 404);
    //     }

    //     $hocSinh->delete();
    //     return response()->json(['message' => 'Xóa học sinh thành công'], 200);
    try {
        $hocSinh = HocSinh::findOrFail($id);
        $hocSinh->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa học sinh thành công!'
        ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể xóa học sinh vì còn dữ liệu liên quan!'
            ], 400);
        }
     }
}
