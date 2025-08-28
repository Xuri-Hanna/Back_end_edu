<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CongNo extends Model
{
    use HasFactory;

    protected $table = 'cong_no';

    protected $fillable = [
        'tien_no',
        'da_tra',
    ];

    // Một công nợ thuộc về 1 hợp đồng
    public function hopDong()
    {
        return $this->hasOne(HopDongThuePhong::class, 'cong_no_id', 'id');
    }
}
