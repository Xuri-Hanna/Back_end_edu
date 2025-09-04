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
            'nhan_vien_id'  => 'required|exists:nhan_vien,id',
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
            'nhan_vien_id'  => $request->nhan_vien_id
        ]);

        return response()->json([
            'message' => 'Tạo hóa đơn thành công',
            'data' => $hoaDon
        ], 201);
    }

     // Xem danh sách hóa đơn theo mã hợp đồng
    public function getByHopDong($hopDongId)
    {
        $hoaDons = HoaDonThuePhong::with('nhanVien:id,ho_ten')
            ->where('hop_dong_id', $hopDongId)
            ->get();

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
