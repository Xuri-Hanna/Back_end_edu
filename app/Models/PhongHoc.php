<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhongHoc extends Model
{
    protected $table = 'phong_hoc';

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false; // id là char(10), không auto-increment

    public $timestamps = true; // nếu bạn bỏ timestamps trong migration thì để false

   protected $fillable = [
    'id', 'so_phong', 'vi_tri_phong', 'so_cho_ngoi','gia_phong', 'ghi_chu'
    ];

}
