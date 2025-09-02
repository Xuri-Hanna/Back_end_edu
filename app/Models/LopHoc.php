<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\LichDay;

class LopHoc extends Model
{
    protected $table = 'lop_hoc';

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false; // id là char(10)
    public $timestamps = true;

    protected $fillable = [
        'id',
        'ten_lop',
        'nam_hoc',
        'mon_hoc_id',
        'giao_vien_id',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'so_luong',
        'phong_hoc_id',
        'trang_thai',
        'don_gia',
        'so_buoi'
    ];

    public function monHoc()
    {
        return $this->belongsTo(MonHoc::class, 'mon_hoc_id', 'id');
    }

    public function giaoVien()
    {
        return $this->belongsTo(GiaoVien::class, 'giao_vien_id', 'id');
    }

    public function phongHoc()
    {
        return $this->belongsTo(PhongHoc::class, 'phong_hoc_id', 'id');
    }
    public function chiTietLop()
    {
        return $this->hasMany(ChiTietLopHoc::class, 'lop_hoc_id', 'id');
    }

    public function hocSinhs()
    {
        return $this->belongsToMany(HocSinh::class, 'chi_tiet_lop_hoc', 'lop_hoc_id', 'hoc_sinh_id');
    }
    public function lichDays()
    {
        return $this->hasMany(LichDay::class, 'lop_hoc_id', 'id');
    }
    public function hoaDonHocPhis()
    {
        return $this->hasMany(HoaDonHocPhi::class, 'lop_hoc_id', 'id');
    }
    public function hoaDons()
    {
        return $this->hasMany(HoaDonHocPhi::class, 'lop_hoc_id', 'id');
    }
    // app/Models/LopHoc.php
    public function giaoViens()
    {
        return $this->belongsTo(GiaoVien::class, 'giao_vien_id');
    }

}
