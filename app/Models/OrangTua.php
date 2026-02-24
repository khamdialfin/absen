<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrangTua extends Model
{
    protected $table = 'orang_tua';
    
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'address'
    ];

     public function siswa()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
