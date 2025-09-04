<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TaiKhoan;
use App\Models\GiaoVien;
use App\Models\NhanVien;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class TaiKhoanController extends Controller
{
    // Lấy danh sách tất cả tài khoản
    public function index()
    {
        return response()->json(TaiKhoan::all(), 200);
    }

    // Lấy danh sách tài khoản chưa sử dụng
    public function getUnused()
    {
        $list = TaiKhoan::where('trang_thai', 'Chưa sử dụng')->get();
        return response()->json($list, 200);
    }

    // Thêm mới tài khoản
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:tai_khoan,username',
            'password' => 'required|string|min:6',
        ]);

        $id = Str::random(10);

        // Mặc định trạng thái là "chưa sử dụng"
        $trang_thai = 'Chưa sử dụng';

        // Kiểm tra liên kết với giáo viên hoặc nhân viên
        $giaoVienExists = GiaoVien::where('tai_khoan_id', $id)->exists();
        $nhanVienExists = NhanVien::where('tai_khoan_id', $id)->exists();

        if ($giaoVienExists || $nhanVienExists) {
            $trang_thai = 'Đã sử dụng';
        }

        $taikhoan = TaiKhoan::create([
            'ID' => $id,
            'username' => $request->username,
            'password' => $request->password,
            'trang_thai' => $trang_thai,
        ]);

        return response()->json([
            'message' => 'Tạo tài khoản thành công',
            'data' => $taikhoan
        ], 201);
    }

    // Lấy thông tin 1 tài khoản
    public function show($id)
    {
        $tk = TaiKhoan::find($id);
        if (!$tk) {
            return response()->json(['message' => 'Không tìm thấy tài khoản'], 404);
        }
        return response()->json($tk, 200);
    }

    // Cập nhật tài khoản
    public function update(Request $request, $id)
    {
        $tk = TaiKhoan::find($id);
        if (!$tk) {
            return response()->json(['message' => 'Không tìm thấy tài khoản'], 404);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:50',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $tk->update($request->all());
        return response()->json([
            'message' => 'Cập nhật tài khoản thành công',
            'data' => $tk
        ], 200);
    }

    // Xóa tài khoản
    public function destroy($id)
    {
        $tk = TaiKhoan::find($id);
        if (!$tk) {
            return response()->json(['message' => 'Không tìm thấy tài khoản'], 404);
        }
        $tk->delete();
        return response()->json(['message' => 'Xóa tài khoản thành công'], 200);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6',
            'confirm_password' => 'required|string|same:new_password',

        ]);

        $user = TaiKhoan::where('username', $request->username)->first();

        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy tài khoản'], 404);
        }

        if ($request->old_password !== $user->password) {
            return response()->json(['message' => 'Mật khẩu hiện tại không đúng'], 400);
        }

        $user->password = $request->new_password;
        $user->save();

        return response()->json(['message' => 'Đổi mật khẩu thành công!'], 200);
    }
}
