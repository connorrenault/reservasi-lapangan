<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini secara eksplisit.
     *
     * @var string
     */
    protected $table = 'pembayaran';

    /**
     * Primary key kustom yang digunakan oleh tabel pembayaran.
     *
     * @var string
     */
    protected $primaryKey = 'id_pembayaran';

    /**
     * Atribut yang diizinkan untuk pengisian massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_reservasi',
        'metode_pembayaran',
        'jumlah_bayar',
        'bukti_transfer',
        'status_pembayaran',
    ];

    /**
     * Relasi balik ke model Reservasi (Belongs To).
     * Setiap data transaksi pembayaran ini merujuk pada satu data reservasi tertentu.
     */
    public function reservasi()
    {
        return $this->belongsTo(Reservasi::class, 'id_reservasi', 'id_reservasi');
    }
}