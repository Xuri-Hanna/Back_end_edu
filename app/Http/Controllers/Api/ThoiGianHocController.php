<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ThoiGianHoc;

class ThoiGianHocController extends Controller
{
    // Lấy danh sách
    public function index()
    {
        $data = ThoiGianHoc::all();
        return response()->json($data);
    }

    // Thêm mới
    public function store(Request $request)
    {
        $request->validate([
            'buoi' => 'required|string|max:50',
            'gio_bat_dau' => 'required',
            'gio_ket_thuc' => 'required',
        ]);

        $item = ThoiGianHoc::create($request->all());

        return response()->json([
            'message' => 'Thêm thành công',
            'data' => $item
        ], 201);
    }

    // Xem chi tiết 1 bản ghi
    public function show($id)
    {
        $item = ThoiGianHoc::findOrFail($id);
        return response()->json($item);
    }

    // Cập nhật
    public function update(Request $request, $id)
    {
        $item = ThoiGianHoc::findOrFail($id);

        $request->validate([
            'buoi' => 'sometimes|string|max:50',
            'gio_bat_dau' => 'sometimes',
            'gio_ket_thuc' => 'sometimes',
        ]);

        $item->update($request->all());

        return response()->json([
            'message' => 'Cập nhật thành công',
            'data' => $item
        ]);
    }

    // Xóa
    public function destroy($id)
    {
        $item = ThoiGianHoc::findOrFail($id);
        $item->delete();

        return response()->json([
            'message' => 'Xóa thành công'
        ]);
    }
}
