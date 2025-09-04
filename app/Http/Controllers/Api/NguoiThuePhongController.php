<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\NguoiThuePhong;
use Illuminate\Http\Request;

class NguoiThuePhongController extends Controller
{
    // Lấy danh sách người thuê phòng
    public function index()
    {
        $dsNguoiThue = NguoiThuePhong::all();
        return response()->json($dsNguoiThue);
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $nguoiThue = NguoiThuePhong::when($keyword, function ($query, $keyword) {
            $query->where('id', 'like', "%{$keyword}%")
                ->orWhere('ho_ten', 'like', "%{$keyword}%");
        })->get();

        return response()->json($nguoiThue, 200);
    }

    // Thêm mới người thuê phòng
    public function store(Request $request)
    {
        $request->validate([
            'ho_ten'       => 'required|string|max:255',
            'gioi_tinh'    => 'required|in:Nam,Nữ,Khác',
            'so_dien_thoai'=> 'required|string|max:20|unique:nguoi_thue_phong,so_dien_thoai',
            'cccd'         => 'required|string|max:20|unique:nguoi_thue_phong,cccd',
            'dia_chi'      => 'nullable|string',
            'email'        => 'nullable|email|max:255|unique:nguoi_thue_phong,email',
        ]);

        // Sinh id tự động: NTP001, NTP002...
        $lastId = NguoiThuePhong::orderBy('id', 'desc')->value('id');
        if ($lastId) {
            $number = (int) substr($lastId, 3) + 1;
        } else {
            $number = 1;
        }
        $newId = 'NTP' . str_pad($number, 3, '0', STR_PAD_LEFT);

        $nguoiThue = NguoiThuePhong::create([
            'id'            => $newId,
            'ho_ten'        => $request->ho_ten,
            'gioi_tinh'     => $request->gioi_tinh,
            'so_dien_thoai' => $request->so_dien_thoai,
            'cccd'          => $request->cccd,
            'dia_chi'       => $request->dia_chi,
            'email'         => $request->email,
        ]);

        return response()->json([
            'message' => 'Thêm người thuê phòng thành công',
            'data'    => $nguoiThue
        ], 201);
    }

    // Cập nhật thông tin người thuê phòng
    public function update(Request $request, $id)
    {
        $nguoiThue = NguoiThuePhong::findOrFail($id);

        $request->validate([
            'ho_ten'       => 'required|string|max:255',
            'gioi_tinh'    => 'required|in:Nam,Nữ,Khác',
            'so_dien_thoai'=> 'required|string|max:20|unique:nguoi_thue_phong,so_dien_thoai,' . $id . ',id',
            'cccd'         => 'required|string|max:20|unique:nguoi_thue_phong,cccd,' . $id . ',id',
            'dia_chi'      => 'nullable|string',
            'email'        => 'nullable|email|max:255|unique:nguoi_thue_phong,email,' . $id . ',id',
        ]);

        $nguoiThue->update($request->only([
            'ho_ten',
            'gioi_tinh',
            'so_dien_thoai',
            'cccd',
            'dia_chi',
            'email',
        ]));

        return response()->json([
            'message' => 'Cập nhật người thuê phòng thành công',
            'data'    => $nguoiThue
        ]);
    }

    // Xóa người thuê phòng
    public function destroy($id)
    {
        // $nguoiThue = NguoiThuePhong::findOrFail($id);
        // $nguoiThue->delete();

        // return response()->json([
        //     'message' => 'Xóa người thuê phòng thành công'
        // ]);
    try {
        $nguoiThue = NguoiThuePhong::findOrFail($id);
        $nguoiThue->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa người thuê phòng thành công!'
        ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể xóa người thuê phòng vì còn dữ liệu liên quan!'
            ], 400);
        }
    }
}
