<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;
    protected $table = 'peminjamen'; 
    protected $fillable = [
        'jenis_dokumen',         
        'nomor_dokumen',
        'peminjam',
        'tanggal_peminjaman',
        'tanggal_pengembalian',
        'status',   
        'regency_id',    
        'district_id',   // Menyimpan ID Kecamatan
        'village_id',  
    ];

    public function pinjam()
    {
        $this->status = 'Dipinjam';
        $this->save();
    }

    public function kembalikan()
    {
        $this->tanggal_pengembalian = now();
        $this->status = 'Dikembalikan';
        $this->save();
    }

    // Relasi ke model Kabupaten (Regency)
    public function regency()
    {
        return $this->belongsTo(Regency::class, 'regency_id');
    }

    // Relasi ke model Kecamatan (District)
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    // Relasi ke model Desa (Village)
    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id');
    }
    public function getDendaAttribute()
{
    $dendaPerHari = 50000;

    // Jika status masih 'Dipinjam' dan tanggal_pengembalian belum diisi
    if ($this->status === 'Dipinjam' && $this->tanggal_pengembalian === null) {
        return 0; // Tidak ada denda jika belum dikembalikan
    }

    // Jika tanggal_pengembalian sudah ada dan lebih kecil dari sekarang, hitung denda
    if ($this->tanggal_pengembalian && $this->tanggal_pengembalian < now()) {
        $hariTerlambat = now()->diffInDays($this->tanggal_pengembalian);
        return $hariTerlambat * $dendaPerHari;
    }

    return 0;
}


}
