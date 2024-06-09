<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangkesenianM extends Model
{
    use HasFactory;

    public $table = 'barangkesenian_m';
    protected $fillable = [
        'nama',
        'jenis',
        'hargasewa',
        'deskripsi',
        'stok',
        'foto',
    ];

}
