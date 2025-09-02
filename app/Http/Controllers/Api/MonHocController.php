<?php

namespace App\Http\Controllers\Api;

use App\Models\MonHoc;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MonHocController extends Controller
{
    // Lấy danh sách môn học
    public function index()
    {
        $monHocs = MonHoc::all();
        return response()->json($monHocs);
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $monHocs = MonHoc::when($keyword, function ($query, $keyword) {
            $query->where('id', 'like', "%{$keyword}%")
                ->orWhere('mon_hoc', 'like', "%{$keyword}%")
                ->orWhere('khoi_lop', 'like', "%{$keyword}%");
        })->get();

        return response()->json($monHocs, 200);
    }

    // Lấy chi tiết 1 môn học theo id
    public function show($id)
    {
        $monHoc = MonHoc::find($id);
        if (!$monHoc) {
            return response()->json(['message' => 'Môn học không tồn tại'], 404);
        }
        return response()->json($monHoc);
    }

    // Thêm môn học mới
   public function store(Request $request)
    {
        $request->validate([
            'mon_hoc'  => 'required|string|max:255',
            'khoi_lop' => 'required|string|max:50',
            'nam_hoc'  => 'required|string|max:20',
        ]);

        // Kiểm tra trùng (nếu cả 3 trường giống nhau thì không cho thêm)
        $exists = MonHoc::where('mon_hoc', $request->mon_hoc)
            ->where('khoi_lop', $request->khoi_lop)
            ->where('nam_hoc', $request->nam_hoc)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Môn học này đã tồn tại trong cùng khối và năm học!'
            ], 422);
        }

        // Lấy ID lớn nhất hiện tại
        $lastId = MonHoc::orderBy('id', 'desc')->value('id');

        if ($lastId) {
            $number = (int) substr($lastId, 2) + 1; // bỏ "MH" và tăng số
        } else {
            $number = 1;
        }

        // Format thành MH001, MH002,...
        $newId = 'MH' . str_pad($number, 3, '0', STR_PAD_LEFT);

        // Tạo mới môn học
        $monHoc = MonHoc::create([
            'id'       => $newId,
            'mon_hoc'  => $request->mon_hoc,
            'khoi_lop' => $request->khoi_lop,
            'nam_hoc'  => $request->nam_hoc,
        ]);

        return response()->json([
            'message' => 'Thêm môn học thành công',
            'data'    => $monHoc
        ]);
    }

    // Cập nhật môn học
    public function update(Request $request, $id)
    {
        $monHoc = MonHoc::find($id);
        if (!$monHoc) {
            return response()->json(['message' => 'Môn học không tồn tại'], 404);
        }

        $request->validate([
            'mon_hoc' => 'sometimes|required|string|max:255',
            'khoi_lop' => 'sometimes|required|string|max:50',
            'nam_hoc' => 'sometimes|required|string|max:50',
        ]);

        $monHoc->update($request->all());

        return response()->json([
            'message' => 'Cập nhật môn học thành công',
            'data' => $monHoc
        ]);
    }

    // Xóa môn học
    public function destroy($id)
    {
        $monHoc = MonHoc::find($id);
        if (!$monHoc) {
            return response()->json(['message' => 'Môn học không tồn tại'], 404);
        }

        $monHoc->delete();

        return response()->json(['message' => 'Xóa môn học thành công']);
    }
}
