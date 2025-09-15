<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThoiGianHoc extends Model
{
    protected $table = 'thoi_gian_hoc';       // Tên bảng
    protected $primaryKey = 'id';         // Tên khóa chính
    protected $keyType = 'int';        // Kiểu khóa chính là string
    public $incrementing = true;         // Không tự tăng
    public $timestamps = false;           // Không có cột created_at/updated_at

    protected $fillable = [
        'id',
        'buoi',
        'gio_bat_dau',
        'gio_ket_thuc'
    ];
}
