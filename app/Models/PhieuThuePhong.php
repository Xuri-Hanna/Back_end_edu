<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhieuThuePhong extends Model
{
    protected $table = 'phieu_thue_phong';

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false; // id là char(10), không tự tăng

    public $timestamps = true;

    protected $fillable = [
        'id',
        'nhan_vien_id',
        'nguoi_thue_phong_id',
        'phong_hoc_id',
        'tu_ngay',
        'den_ngay',
        'lich_thue',
        'trang_thai'
    ];

    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'nhan_vien_id', 'id');
    }

    public function nguoiThuePhong()
    {
        return $this->belongsTo(NguoiThuePhong::class, 'nguoi_thue_phong_id', 'id');
    }

    public function phongHoc()
    {
        return $this->belongsTo(PhongHoc::class, 'phong_hoc_id', 'id');
    }
    public function hopDongThuePhong()
    {
        return $this->hasOne(HopDongThuePhong::class, 'phieu_thue_phong_id', 'id');
    }

    public function getTrangThaiLabelAttribute()
    {
        return $this->trang_thai === 'Đã có hợp đồng'
            ? '✅ Đã có hợp đồng'
            : '⏳ Chưa có hợp đồng';
    }
}
