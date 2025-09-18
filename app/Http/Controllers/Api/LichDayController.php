<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\LichDay;
use App\Models\LopHoc;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LichDayController extends Controller
{
    // Trả về lịch của 1 lớp
    public function index($lopHocId)
    {
        return LichDay::where('lop_hoc_id', $lopHocId)->get();
    }

    // Tick/untick: value=true -> tạo (nếu chưa có), value=false -> xóa
    public function toggle(Request $request, $lopHocId)
    {
        $validated = $request->validate([
            'thu'  => ['required', Rule::in(['T2','T3','T4','T5','T6','T7','CN'])],
            'buoi' => ['required', Rule::in(['morning','afternoon','evening'])],
            'value'=> ['required','boolean'],
        ]);

        if ($validated['value'] === true) {
            // tạo nếu chưa có
            LichDay::firstOrCreate([
                'lop_hoc_id' => $lopHocId,
                'thu'        => $validated['thu'],
                'buoi'       => $validated['buoi'],
            ]);
            return response()->json(['message' => 'Đã thêm lịch'], 201);
        } else {
            // xóa nếu có
            LichDay::where('lop_hoc_id', $lopHocId)
                ->where('thu', $validated['thu'])
                ->where('buoi', $validated['buoi'])
                ->delete();
            return response()->json(['message' => 'Đã xóa lịch']);
        }
    }
    public function lichDayDangHoc()
    {
        $lops = LopHoc::with(['giaoViens', 'lichDays','phongHoc'])
            ->where('trang_thai', 'Đang học')
            ->get();

        // Sắp xếp theo số phòng (ví dụ P101 → 101)
        $sorted = $lops->sortBy(function($lop) {
            if (!$lop->phongHoc || !$lop->phongHoc->so_phong) return PHP_INT_MAX;
            // Lấy phần số từ chuỗi P101
            return (int) preg_replace('/[^0-9]/', '', $lop->phongHoc->so_phong);
        })->values();
        return response()->json($sorted);
    }
     public function lichDayGiaoVien($giao_vien_id)
    {
        if($giao_vien_id){
            $lops = LopHoc::with(['giaoViens', 'lichDays','phongHoc'])
                ->where('giao_vien_id',$giao_vien_id)
                ->whereIn('trang_thai', ['Đang học','Sắp mở'])
                ->get();
        }
        return response()->json($lops);
    }
    // App\Http\Controllers\LopHocController.php
   public function indexWithSchedule()
    {
        $lopHocs = LopHoc::with(['lichDays', 'giaoViens', 'phongHoc'])
            ->where('trang_thai', 'Đang học')
            ->get();

        $result = $lopHocs->map(function ($lop) {
            return [
                'id' => $lop->id,
                'ten_lop' => $lop->ten_lop,
                'ten_giao_vien' => optional($lop->giaoVien)->ten_giao_vien, // tránh lỗi null
                'lich_day' => $lop->lichDay
                    ? $lop->lichDay->map(function ($lich) {
                        return [
                            'thu' => $lich->thu,
                            'buoi' => $lich->buoi,
                        ];
                    })
                    : [] // nếu null thì trả về mảng rỗng
            ];
        });

        return response()->json($result);
    }

}
