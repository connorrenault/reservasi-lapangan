<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservasi extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini secara eksplisit.
     *
     * @var string
     */
    protected $table = 'reservasi';

    /**
     * Primary key kustom yang digunakan oleh tabel reservasi.
     *
     * @var string
     */
    protected $primaryKey = 'id_reservasi';

    /**
     * Menggunakan guarded kosong agar semua kolom baru diizinkan 
     * untuk disimpan ke database tanpa terblokir Mass Assignment.
     */
    protected $guarded = [];

    /**
     * Relasi balik ke model User (Belongs To).
     * Setiap reservasi dibuat oleh satu user/customer.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Relasi balik ke model Lapangan (Belongs To).
     * Setiap reservasi merujuk pada satu lapangan tertentu.
     */
    public function lapanganRelasi()
    {
        return $this->belongsTo(Lapangan::class, 'id_lapangan', 'id_lapangan');
    }

    /**
     * Relasi balik ke model Jadwal (Belongs To).
     * Setiap reservasi menempati satu slot jadwal/jam tertentu.
     */
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'id_jadwal', 'id_jadwal');
    }

    /**
     * Relasi ke model Pembayaran (One to One).
     * Satu reservasi hanya memiliki satu data transaksi pembayaran.
     */
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'id_reservasi', 'id_reservasi');
    }
}