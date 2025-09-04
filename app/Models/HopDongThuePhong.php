<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HopDongThuePhong extends Model
{
    use HasFactory;

    protected $table = 'hop_dong_thue_phong';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'char';

    protected $fillable = [
        'id',
        'phieu_thue_phong_id',
        'dieu_khoan',
        'thanh_tien',
        'cong_no_id',
        'ngay_lap',
        'han_hop_dong'
    ];

    // Quan hệ: Hợp đồng thuộc về phiếu thuê phòng
    public function phieuThuePhong()
    {
        return $this->belongsTo(PhieuThuePhong::class, 'phieu_thue_phong_id', 'id');
    }
    public function congNo()
    {
        return $this->belongsTo(CongNo::class, 'cong_no_id', 'id');
    }
}
