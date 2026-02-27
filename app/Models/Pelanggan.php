<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = "pelanggan";

    protected $primaryKey = 'id_pelanggan'; // ← TAMBAHKAN INI

    public $incrementing = true; // kalau auto increment

    protected $fillable = [
        'nama_pelanggan'
    ];

    public function detail_laundry()
    {
        return $this->hasMany(DetailLaundry::class, 'id_pelanggan', 'id_pelanggan');
    }

    // app/Models/Pelanggan.php
    public function grup()
    {
        return $this->belongsToMany(Grup::class, 'detail_laundry', 'id_pelanggan', 'id_grup');
    }
}
