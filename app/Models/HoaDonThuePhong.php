<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HoaDonThuePhong extends Model
{
    protected $table = 'hoa_don_thue_phong';

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    public $timestamps = true;

    protected $fillable = [
        'id',
        'hop_dong_id',
        'ngay_lap',
        'tong_tien',
    ];

    public function hopDongThuePhong()
    {
        return $this->belongsTo(HopDongThuePhong::class, 'hop_dong_id', 'id');
    }
    // Khi hóa đơn được tạo -> update công nợ
    protected static function booted()
    {
        static::created(function ($hoaDon) {
            $hopDong = $hoaDon->hopDongThuePhong;

            if ($hopDong && $hopDong->congNo) {
                $congNo = $hopDong->congNo;

                // Cộng thêm vào số đã trả
                $congNo->da_tra += $hoaDon->tong_tien;

                // Tính lại tiền nợ
                $congNo->tien_no = $congNo->tien_no - $hoaDon->tong_tien;

                $congNo->save();
            // Nếu đã trả hết => cập nhật trạng thái hợp đồng
            if ($congNo->tien_no <= 0) {
                $hopDong->trang_thai = 'Đã thanh toán';
                $hopDong->save();
            }
            }
        });
    }
}
