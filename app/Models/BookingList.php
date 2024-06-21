<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingList extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'barangkesenian_id',
        'user_id',
        'date',
        'alamat',
        'bukti_pembayaran',
        'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    public function kesenian(){
        return $this->hasOne(BarangkesenianM::class, 'id', 'barangkesenian_id');
    }

    public function room(){
        return $this->hasOne(BarangkesenianM::class, 'id', 'barangkesenian_id');
    }

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
