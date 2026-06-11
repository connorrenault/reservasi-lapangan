<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lapangan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini secara eksplisit.
     *
     * @var string
     */
    protected $table = 'lapangan';

    /**
     * Primary key kustom yang digunakan oleh tabel lapangan.
     *
     * @var string
     */
    protected $primaryKey = 'id_lapangan';

    /**
     * Atribut yang diizinkan untuk pengisian massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_lapangan',
        'jenis_olahraga',
        'harga_per_jam',
        'deskripsi',
        'status',
    ];

    /**
     * Relasi ke model Jadwal (One to Many).
     * Satu lapangan memiliki banyak slot jadwal operasional.
     */
    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'id_lapangan', 'id_lapangan');
    }

    /**
     * Relasi ke model Reservasi (One to Many).
     * Satu lapangan bisa dipesan di dalam banyak data reservasi.
     */
    public function reservasi()
    {
        return $this->hasMany(Reservasi::class, 'id_lapangan', 'id_lapangan');
    }
}