<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GiaoVien;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;

class GiaoVienController extends Controller
{
    // Lấy danh sách giáo viên
    public function index(Request $request)
    {
        $query = GiaoVien::with(['donViCongTac', 'taiKhoan']);
        $giaoVien = $query->get();
        return response()->json($giaoVien);
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $giaoVien = GiaoVien::with(['donViCongTac', 'taiKhoan'])
            ->when($keyword, function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('id', 'like', "%{$keyword}%")
                    ->orWhere('ho_ten', 'like', "%{$keyword}%");
                });
            })
            ->get();

        return response()->json($giaoVien);
    }

    // Lấy thông tin 1 giáo viên
    public function show($id)
    {
        $giaoVien = GiaoVien::with(['donViCongTac', 'taiKhoan'])->find($id);
        if (!$giaoVien) {
            return response()->json(['message' => 'Không tìm thấy giáo viên'], 404);
        }
        return response()->json($giaoVien);
    }

    // Thêm giáo viên mới
    public function store(Request $request)
    {
        $request->validate([
            'tai_khoan_id'       => 'nullable|string|exists:tai_khoan,ID',
            'chuc_vu_id'         => 'nullable|string|exists:chuc_vu,id',
            'ho_ten'             => 'required|string|max:255',
            'cccd'               => 'required|string|max:20',
            'dia_chi'            => 'required|string',
            'so_dien_thoai'      => 'required|string|max:20',
            'email'              => 'required|email|max:255',
            'don_vi_cong_tac_id' => 'nullable|string|exists:don_vi_cong_tac,id',
        ]);

        // Lấy ID lớn nhất hiện tại
        $lastId = GiaoVien::orderBy('id', 'desc')->value('id');

        if ($lastId) {
            $number = (int) substr($lastId, 2) + 1;
        } else {
            $number = 1;
        }

        // Format thành GV001, GV002,...
        $newId = 'GV' . str_pad($number, 3, '0', STR_PAD_LEFT);

        $giaoVien = GiaoVien::create([
            'id'                 => $newId,
            'tai_khoan_id'       => $request->tai_khoan_id,
            'chuc_vu_id'         => $request->chuc_vu_id,
            'ho_ten'             => $request->ho_ten,
            'cccd'               => $request->cccd,
            'dia_chi'            => $request->dia_chi,
            'so_dien_thoai'      => $request->so_dien_thoai,
            'email'              => $request->email,
            'don_vi_cong_tac_id' => $request->don_vi_cong_tac_id,
        ]);

        // Cập nhật trạng thái tài khoản
        TaiKhoan::where('ID', $request->tai_khoan_id)
                ->update(['trang_thai' => 'Đã sử dụng']);

        //return response()->json($nhanVien, 201);

        return response()->json($giaoVien, 201);
    }

    // Cập nhật giáo viên
    public function update(Request $request, $id)
    {
        $giaoVien = GiaoVien::find($id);
        if (!$giaoVien) {
            return response()->json(['message' => 'Không tìm thấy giáo viên'], 404);
        }

        $request->validate([
            'tai_khoan_id'       => 'nullable|string|exists:tai_khoan,ID',
            'chuc_vu_id'         => 'nullable|string|exists:chuc_vu,id',
            'ho_ten'             => 'required|string|max:255',
            'cccd'               => 'required|string|max:20',
            'dia_chi'            => 'required|string',
            'so_dien_thoai'      => 'required|string|max:20',
            'email'              => 'required|email|max:255',
            'don_vi_cong_tac_id' => 'nullable|string|exists:don_vi_cong_tac,id',
        ]);

        // $giaoVien->update($request->all());

        // TaiKhoan::where('ID', $request->tai_khoan_id)
        //         ->update(['trang_thai' => 'Đã sử dụng']);

        // return response()->json($giaoVien);

         if ($request->has('tai_khoan_id') && $request->tai_khoan_id != $giaoVien->tai_khoan_id) {
            // Mở lại tài khoản cũ
            if ($giaoVien->tai_khoan_id) {
                TaiKhoan::where('ID', $giaoVien->tai_khoan_id)
                    ->update(['trang_thai' => 'Chưa sử dụng']);
            }

            // Đánh dấu tài khoản mới
            TaiKhoan::where('ID', $request->tai_khoan_id)
                ->update(['trang_thai' => 'Đã sử dụng']);
        }

        $giaoVien->update($request->all());

        return response()->json($giaoVien);

    }

    // Xóa giáo viên
    public function destroy($id)
    {
        // $giaoVien = GiaoVien::find($id);
        // if (!$giaoVien) {
        //     return response()->json(['message' => 'Không tìm thấy giáo viên'], 404);
        // }

        // $giaoVien->delete();

        // return response()->json(['message' => 'Đã xóa giáo viên']);

        try {
        $giaoVien = GiaoVien::findOrFail($id);

        // Trả lại trạng thái cho tài khoản của nhân viên
        if ($giaoVien->tai_khoan_id) {
            TaiKhoan::where('ID', $giaoVien->tai_khoan_id)
                ->update(['trang_thai' => 'Chưa sử dụng']);
        }

        $giaoVien->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa giáo viên thành công!'
        ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể xóa giáo viên vì còn dữ liệu liên quan!'
            ], 400);
        }
    }
}
