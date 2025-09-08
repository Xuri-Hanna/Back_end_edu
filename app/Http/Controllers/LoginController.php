<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaiKhoan;
use App\Models\NhanVien;
use App\Models\GiaoVien;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Tìm tài khoản theo username
        $taiKhoan = TaiKhoan::where('username', $request->username)->first();

        if (!$taiKhoan) {
            return response()->json(['message' => 'Tài khoản không tồn tại'], 401);
        }

        // So sánh mật khẩu dạng plain text
        if ($request->password !== $taiKhoan->password) {
            return response()->json(['message' => 'Sai mật khẩu'], 401);
        }

        $role = null;
        $userInfo = null;
        $fullName = null;
        

        // Kiểm tra nếu là nhân viên
        $nhanVien = NhanVien::where('tai_khoan_id', $taiKhoan->ID)->first();
        if ($nhanVien) {
            $role = $nhanVien->chuc_vu_id;
            $userInfo = $nhanVien;
            $fullName = $nhanVien->ho_ten;
            
        }

        // Nếu không phải nhân viên, kiểm tra giáo viên
        if (!$nhanVien) {
            $giaoVien = GiaoVien::where('tai_khoan_id', $taiKhoan->ID)->first();
            if ($giaoVien) {
                $role = $giaoVien->chuc_vu_id;
                $userInfo = $giaoVien;
                $fullName = $giaoVien->ho_ten;
            }
        }

        if (!$role) {
            return response()->json([
                'message' => 'Không tìm thấy quyền của người dùng'
            ], 403);
        }

        // Tạo token
        // $token = $taiKhoan->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'role' => $role,
            'id' => $userInfo->id,
            'user' => $userInfo,
            'full_name' => (string) $fullName,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Đăng xuất thành công']);
    }
}
