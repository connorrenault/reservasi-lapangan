<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini secara eksplisit.
     *
     * @var string
     */
    protected $table = 'jadwal';

    /**
     * Primary key kustom yang digunakan oleh tabel jadwal.
     *
     * @var string
     */
    protected $primaryKey = 'id_jadwal';

    /**
     * Atribut yang diizinkan untuk pengisian massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_lapangan',
        'jam_mulai',
        'jam_selesai',
    ];

    /**
     * Relasi balik ke model Lapangan (Belongs To).
     * Setiap slot jadwal ini dimiliki oleh satu lapangan tertentu.
     */
    public function lapangan()
    {
        return $this->belongsTo(Lapangan::class, 'id_lapangan', 'id_lapangan');
    }

    /**
     * Relasi ke model Reservasi (One to Many).
     * Satu slot jadwal bisa dipakai di banyak data reservasi (pada tanggal yang berbeda).
     */
    public function reservasi()
    {
        return $this->hasMany(Reservasi::class, 'id_jadwal', 'id_jadwal');
    }
}