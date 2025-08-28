<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HoaDonThuePhong;

class HoaDonThuePhongController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'hop_dong_id' => 'required|exists:hop_dong_thue_phong,id',
            'ngay_lap' => 'required|date',
            'tong_tien' => 'required|numeric|min:0',
        ]);

        // Tạo ID tự động (HDTxxx)
        $last = HoaDonThuePhong::orderBy('id', 'desc')->first();

        if ($last) {
            $lastNumber = intval(substr($last->id, 3));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $newId = 'HDT' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        $hoaDon = HoaDonThuePhong::create([
            'id' => $newId,
            'hop_dong_id' => $request->hop_dong_id,
            'ngay_lap' => $request->ngay_lap,
            'tong_tien' => $request->tong_tien,
        ]);
             // --- Cập nhật công nợ ---
        $hopDong = \App\Models\HopDongThuePhong::with('congNo')->find('hop_dong_id');

        if ($hopDong && $hopDong->congNo) {
            $congNo = $hopDong->congNo;

            // Cập nhật số tiền đã trả
            $congNo->da_tra += 'tong_tien';

            // Cập nhật tiền nợ = tổng nợ - đã trả
            $congNo->tien_no = max(0, $congNo->tien_no - 'tong_tien');
            $congNo->save();

            // Nếu không còn nợ thì đổi trạng thái hợp đồng
            if ($congNo->tien_no == 0) {
                $hopDong->trang_thai = 'Đã thanh toán';
                $hopDong->save();
            }
        }

        return response()->json([
            'message' => 'Tạo hóa đơn thành công',
            'data' => $hoaDon
        ], 201);
    }

     // Xem danh sách hóa đơn theo mã hợp đồng
    public function getByHopDong($hopDongId)
    {
        $hoaDons = HoaDonThuePhong::where('hop_dong_id', $hopDongId)->get();
        return response()->json([
            'status' => 'success',
            'data' => $hoaDons
        ]);
    }

    // Xóa hóa đơn theo mã hợp đồng
    public function deleteByHopDong($hopDongId)
    {
        $deleted = HoaDonThuePhong::where('hop_dong_id', $hopDongId)->delete();

        if ($deleted) {
            return response()->json([
                'status' => 'success',
                'message' => "Đã xóa $deleted hóa đơn của hợp đồng $hopDongId"
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => "Không tìm thấy hóa đơn của hợp đồng $hopDongId"
        ], 404);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
