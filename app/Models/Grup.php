<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grup extends Model
{
    use HasFactory;

    protected $table = "grup";

    protected $primaryKey = 'id_grup';
    public $incrementing = true;
    protected $fillable = [
        'id_user',
        'tanggal',
        'jam',
        'kamar',
        'berat',
        'jenis_pakaian',
        'jumlah_orang',
        'status_data'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function pelanggan() {
    return $this->belongsToMany(Pelanggan::class, 'detail_laundry', 'id_grup', 'id_pelanggan')
                ->withPivot(['baju', 'jilbab', 'rok', 'kaos', 'keterangan'])
                ->withTimestamps(); // <--- TAMBAHKAN INI
}

    public function detail_laundry()
    {
        return $this->hasMany(DetailLaundry::class, 'id_grup', 'id_grup');
    }
}
