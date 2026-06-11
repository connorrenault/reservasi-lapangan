<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi data yang dikirim dari form lapangan
        $request->validate([
            'lapangan' => 'required',
            'tanggal_main' => 'required',
            'jam_operasional' => 'required',
            'metode_pembayaran' => 'required',
            'total_bayar' => 'required'
        ]);

        // 2. Membuat Kode Transaksi unik secara otomatis
        $kode_transaksi = 'JPNS-' . rand(100, 999);

        // 3. Ambil data riwayat lama yang ada di session (jika belum ada, buat array kosong)
        $riwayat = session()->get('riwayat_booking', []);

        // 4. Ambil nama user yang login secara otomatis lewat Auth::user()->name
        // Jadi kalau yang login akun DIO, namanya otomatis DIO. Tidak 'CUSTOMER BARU' lagi.
        $nama_user_aktif = Auth::check() ? Auth::user()->name : 'Pelanggan Jopan';

        // 5. Bungkus semua data menjadi objek booking baru
        $bookingBaru = (object)[
            'kode_transaksi' => $kode_transaksi,
            'nama_customer' => $nama_user_aktif, 
            'lapangan' => $request->lapangan,
            'tanggal_main' => date('d M Y', strtotime($request->tanggal_main)),
            'tanggal_mentah' => $request->tanggal_main, // Disimpan untuk link otomatis ke jadwal
            'jam_operasional' => $request->jam_operasional,
            'metode_pembayaran' => $request->metode_pembayaran,
            'total_payar' => $request->total_bayar,
            'status' => 'Pending' // Status awal sebelum dikonfirmasi bayar
        ];

        // 6. Masukkan data baru ke urutan paling atas di dalam array riwayat
        array_unshift($riwayat, $bookingBaru);

        // 7. Simpan kembali daftar riwayat terbaru ke dalam Session
        session()->put('riwayat_booking', $riwayat);

        // 8. Alihkan halaman ke halaman invoice/pembayaran bawa data query string
        return redirect('/pembayaran/' . $kode_transaksi . '?' . http_build_query([
            'lapangan' => $request->lapangan,
            'tanggal' => $request->tanggal_main,
            'jam' => $request->jam_operasional,
            'metode' => $request->metode_pembayaran,
            'total' => $request->total_bayar
        ]));
    }
}