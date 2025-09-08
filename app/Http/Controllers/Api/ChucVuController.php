<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChucVu;
use Illuminate\Support\Str;

class ChucVuController extends Controller
{
    // Lấy danh sách tất cả chức vụ
    public function index()
    {
        return response()->json(ChucVu::all());
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $chucVu = ChucVu::when($keyword, function ($query, $keyword) {
            $query->Where('ten_chuc_vu', 'like', "%{$keyword}%");
        })->get();

        return response()->json($chucVu, 200);
    }

    // Lấy danh sách chức vụ (id + tên) để fill dropdown
    public function getList()
    {
        $chucVus = ChucVu::select('id', 'ten_chuc_vu')->get();
        return response()->json($chucVus, 200);
    }

    // Lấy chi tiết 1 chức vụ theo ID
    public function show($id)
    {
        $chucVu = ChucVu::find($id);

        if (!$chucVu) {
            return response()->json(['message' => 'Không tìm thấy chức vụ'], 404);
        }

        return response()->json($chucVu);
    }

    // Thêm mới chức vụ
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_chuc_vu' => 'required|string|max:255',
            'mo_ta'       => 'nullable|string',
        ]);

        $request->validate([
            'ten_chuc_vu' => 'required|string|max:255',
            'mo_ta'       => 'nullable|string',
        ]);

        // Lấy ID lớn nhất hiện tại
        $lastId = ChucVu::orderBy('id', 'desc')->value('id');

        if ($lastId) {
            $number = (int) substr($lastId, 2) + 1;
        } else {
            $number = 1;
        }

        // Format thành GV001, GV002,...
        $newId = 'CV' . str_pad($number, 3, '0', STR_PAD_LEFT);

        $chucVu = ChucVu::create([
            'id'                 => $newId,
            'ten_chuc_vu'        => $request->ten_chuc_vu,
            'mo_ta'              => $request->mo_ta
        ]);

        return response()->json($chucVu, 201);
    }

    // Cập nhật chức vụ
    public function update(Request $request, $id)
    {
        $chucVu = ChucVu::find($id);

        if (!$chucVu) {
            return response()->json(['message' => 'Không tìm thấy chức vụ'], 404);
        }

        $validated = $request->validate([
            'ten_chuc_vu' => 'required|string|max:255',
            'mo_ta'       => 'nullable|string',
        ]);

        $chucVu->update($validated);

        return response()->json($chucVu);
    }

    // Xóa chức vụ
    public function destroy($id)
    {
        $chucVu = ChucVu::find($id);

        if (!$chucVu) {
            return response()->json(['message' => 'Không tìm thấy chức vụ'], 404);
        }

        $chucVu->delete();

        return response()->json(['message' => 'Xóa thành công']);
    }
}
