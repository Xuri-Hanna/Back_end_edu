<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LichPhong;
use Illuminate\Support\Facades\DB;

class LichPhongController extends Controller
{
    // Lấy lịch của tất cả phòng
    public function index()
    {
        $data = LichPhong::all();
        return response()->json($data);
    }

    // Cập nhật lịch
    public function update(Request $request)
    {
        $lich = LichPhong::updateOrCreate(
            [
                'phong_id' => $request->phong_id,
                'thu' => $request->day,
                'buoi' => $request->slot,
            ],
            ['trang_thai' => $request->value]
        );

        return response()->json($lich);
    }
    // Reset tất cả lịch về "Chưa sử dụng"
    public function reset()
    {
        DB::table('lich_phong')->update(['trang_thai' => 'Chưa sử dụng']);
        return response()->json(['message' => 'Đã reset toàn bộ lịch']);
    }
    public function updateNote(Request $request)
    {
        $lich = LichPhong::updateOrCreate(
            [
                'phong_id' => $request->phong_id,
                'thu' => $request->thu,
                'buoi' => $request->buoi,
            ],
            ['ghi_chu' => $request->ghi_chu]
        );

        return response()->json($lich);
    }


}
