<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NhanVien;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NhanVienController extends Controller
{
    // Lấy danh sách nhân viên
    public function index()
    {
        $nhanVien = NhanVien::with(['chucVu', 'phongBan', 'taiKhoan'])->get();
        return response()->json($nhanVien);
    }

    
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $nhanVien = NhanVien::with(['chucVu', 'phongBan', 'taiKhoan'])
            ->when($keyword, function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('id', 'like', "%{$keyword}%")
                    ->orWhere('ho_ten', 'like', "%{$keyword}%");
                });
            })
            ->get();

        return response()->json($nhanVien);
    }

    // Lấy thông tin 1 nhân viên
    public function show($id)
    {
        $nhanVien = NhanVien::with(['chucVu', 'phongBan', 'taiKhoan'])->find($id);
        if (!$nhanVien) {
            return response()->json(['message' => 'Không tìm thấy nhân viên'], 404);
        }
        return response()->json($nhanVien);
    }


    // Thêm nhân viên mới
   public function store(Request $request)
    {
        $request->validate([
            'tai_khoan_id' => 'nullable|string|exists:tai_khoan,ID',
            'ho_ten'       => 'required|string|max:255',
            'cccd'         => 'required|string|max:20|unique:nhan_vien,cccd',
            'dia_chi'      => 'required|string',
            'so_dien_thoai'=> 'required|string|max:20|unique:nhan_vien,so_dien_thoai',
            'email'        => 'required|email|max:255|unique:nhan_vien,email',
            'chuc_vu_id'   => 'required|string|exists:chuc_vu,id',
            'phong_ban_id' => 'nullable|string|exists:phong_ban,id',
        ], [
            'cccd.unique'          => 'CCCD này đã tồn tại trong hệ thống.',
            'so_dien_thoai.unique' => 'Số điện thoại này đã được sử dụng.',
            'email.unique' => 'Email này đã được sử dụng.',
        ]);

        // Lấy ID lớn nhất hiện tại
        $lastId = NhanVien::orderBy('id', 'desc')->value('id');

        if ($lastId) {
            // Cắt phần số và tăng lên 1
            $number = (int) substr($lastId, 2) + 1;
        } else {
            $number = 1;
        }

        // Format thành NV001, NV002,...
        $newId = 'NV' . str_pad($number, 3, '0', STR_PAD_LEFT);

        $nhanVien = NhanVien::create([
            'id'            => $newId,
            'tai_khoan_id'  => $request->tai_khoan_id,
            'ho_ten'        => $request->ho_ten,
            'cccd'          => $request->cccd,
            'dia_chi'       => $request->dia_chi,
            'so_dien_thoai' => $request->so_dien_thoai,
            'email'         => $request->email,
            'chuc_vu_id'    => $request->chuc_vu_id,
            'phong_ban_id'  => $request->phong_ban_id ?: null,
        ]);
        // Cập nhật trạng thái tài khoản
        TaiKhoan::where('ID', $request->tai_khoan_id)
                ->update(['trang_thai' => 'Đã sử dụng']);

        return response()->json($nhanVien, 201);
    }

    // Cập nhật nhân viên
    public function update(Request $request, $id)
    {
        $nhanVien = NhanVien::find($id);
        if (!$nhanVien) {
            return response()->json(['message' => 'Không tìm thấy nhân viên'], 404);
        }

        $request->validate([
            'tai_khoan_id' => 'nullable|string|exists:tai_khoan,ID',
            'ho_ten'       => 'nullable|string|max:255',
            'cccd'         => 'nullable|string|max:20|unique:nhan_vien,cccd,' . $id . ',id',
            'dia_chi'      => 'nullable|string',
            'so_dien_thoai'=> 'nullable|string|max:20|unique:nhan_vien,so_dien_thoai,'. $id . ',id',
            'email'        => 'nullable|email|max:255|unique:nhan_vien,email,'. $id . ',id',
            'chuc_vu_id'   => 'nullable|string|exists:chuc_vu,id',
            'phong_ban_id' => 'nullable|string|exists:phong_ban,id',
        ], [
            'cccd.unique'          => 'CCCD này đã tồn tại trong hệ thống.',
            'so_dien_thoai.unique' => 'Số điện thoại này đã được sử dụng.',
            'email.unique' => 'Email này đã được sử dụng.',
        ]);

        // $nhanVien->update($request->all());
        // // Cập nhật trạng thái tài khoản
        // TaiKhoan::where('ID', $request->tai_khoan_id)
        //         ->update(['trang_thai' => 'Đã sử dụng']);

        // return response()->json($nhanVien);

        if ($request->has('tai_khoan_id') && $request->tai_khoan_id != $nhanVien->tai_khoan_id) {
            // Mở lại tài khoản cũ
            if ($nhanVien->tai_khoan_id) {
                TaiKhoan::where('ID', $nhanVien->tai_khoan_id)
                    ->update(['trang_thai' => 'Chưa sử dụng']);
            }

            // Đánh dấu tài khoản mới
            TaiKhoan::where('ID', $request->tai_khoan_id)
                ->update(['trang_thai' => 'Đã sử dụng']);
        }

        $nhanVien->update($request->all());

        return response()->json($nhanVien);

    }

    // Xóa nhân viên
    public function destroy($id)
    {
        // $nhanVien = NhanVien::find($id);
        // if (!$nhanVien) {
        //     return response()->json(['message' => 'Không tìm thấy nhân viên'], 404);
        // }
        

        // $nhanVien->delete();

        // return response()->json(['message' => 'Đã xóa nhân viên']);
        try {
        $nhanVien = NhanVien::findOrFail($id);
        
        if ($nhanVien->tai_khoan_id) {
                TaiKhoan::where('ID', $nhanVien->tai_khoan_id)
                    ->update(['trang_thai' => 'Chưa sử dụng']);
        }
        $nhanVien->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa nhân viên thành công!'
        ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể xóa nhân viên vì còn dữ liệu liên quan!'
            ], 400);
        }
    }
}
