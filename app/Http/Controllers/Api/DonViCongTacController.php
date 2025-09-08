<?php

namespace App\Http\Controllers\Api;

use App\Models\DonViCongTac;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DonViCongTacController extends Controller
{
    // Lấy danh sách tất cả đơn vị công tác
    public function index()
    {
        return response()->json(DonViCongTac::all());
    }

     public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $donVi = DonViCongTac::when($keyword, function ($query, $keyword) {
            $query->where('id', 'like', "%{$keyword}%")
                ->orWhere('ten_don_vi', 'like', "%{$keyword}%");
        })->get();

        return response()->json($donVi, 200);
    }


     // Lấy danh sách tất cả đơn vị công tác (sắp xếp A-Z theo tên)
    public function getTenDonVi()
    {
        $donViList = DonViCongTac::select('id', 'ten_don_vi')
            ->orderBy('ten_don_vi', 'asc') // sắp xếp theo tên
            ->get();

        return response()->json($donViList);
    }
    // Tạo mới đơn vị công tác
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_don_vi' => 'required|string|max:255',
            'dia_chi' => 'nullable|string|max:255',
            'so_dien_thoai' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        // Tạo id random 10 ký tự
        $validated['id'] = Str::random(10);

        $donVi = DonViCongTac::create($validated);

        return response()->json($donVi, 201);
    }

    // Xem chi tiết 1 đơn vị công tác
    public function show($id)
    {
        $donVi = DonViCongTac::findOrFail($id);
        return response()->json($donVi);
    }

    // Cập nhật đơn vị công tác
    public function update(Request $request, $id)
    {
        $donVi = DonViCongTac::findOrFail($id);

        $validated = $request->validate([
            'ten_don_vi' => 'sometimes|required|string|max:255',
            'dia_chi' => 'nullable|string|max:255',
            'so_dien_thoai' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $donVi->update($validated);

        return response()->json($donVi);
    }

    // Xóa đơn vị công tác
    public function destroy($id)
    {
        $donVi = DonViCongTac::findOrFail($id);
        $donVi->delete();

        return response()->json(['message' => 'Xóa đơn vị công tác thành công']);
    }
}
