<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailLaundry extends Model
{
    use HasFactory;
    
    protected $table = "detail_laundry";
    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_grup',
        'id_pelanggan',    
        'baju',
        'rok',
        'jilbab',
        'kaos',
        'keterangan'
    ];

    public function grup()
    {
        return $this->belongsTo(Grup::class, 'id_grup', 'id_grup');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }
}
