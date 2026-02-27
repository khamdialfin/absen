<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentProfile extends Model
{
    protected $table = 'parent_profiles';
    
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'address'
    ];

     public function siswa()
    {
        return $this->belongsTo(User::class);
    }
}
