<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservasi;
use App\Models\Lapangan;
use App\Models\Jadwal;
use Illuminate\Support\Facades\Auth;

class ReservasiController extends Controller
{
    /**
     * 1. Menampilkan Form Reservasi / Halaman Detail Lapangan
     */
    public function index($id_lapangan)
    {
        // Mencari data lapangan berdasarkan id_lapangan
        $lapangan = Lapangan::findOrFail($id_lapangan);
        
        // Mengambil semua list jadwal yang tersedia khusus untuk lapangan ini
        $jadwal = Jadwal::where('id_lapangan', $id_lapangan)->get();
        
        return view('reservasi.index', compact('lapangan', 'jadwal'));
    }

    /**
     * 2. Menyimpan data Booking (Reservasi Baru)
     */
    public function store(Request $request)
    {
        // Validasi input data dari form booking
        $request->validate([
            'id_lapangan'     => 'required',
            'id_jadwal'       => 'required',
            'tanggal_booking' => 'required|date|after_or_equal:today',
        ]);

        // Cek apakah jadwal pada tanggal tersebut sudah di-booking orang lain
        $cekBooking = Reservasi::where('id_lapangan', $request->id_lapangan)
            ->where('id_jadwal', $request->id_jadwal)
            ->where('tanggal_booking', $request->tanggal_booking)
            ->where('status_reservasi', '!=', 'dibatalkan')
            ->first();

        // Jika sudah ada booking-an aktif pada slot tersebut, kembalikan dengan pesan error
        if ($cekBooking) {
            return redirect()->back()->with('error', 'Jadwal pada tanggal tersebut sudah dipesan orang lain.');
        }

        // Ambil data detail lapangan untuk melengkapi informasi yang dibutuhkan tabel riwayat
        $lapangan = Lapangan::findOrFail($request->id_lapangan);
        $jadwal = Jadwal::find($request->id_jadwal);

        // Membuat format Jam Operasional (Contoh: "08:00 - 10:00 WIB")
        $jamMulai = $jadwal ? date('H:i', strtotime($jadwal->jam_mulai)) : '08:00';
        $jamSelesai = $jadwal ? date('H:i', strtotime($jadwal->jam_selesai)) : '10:00';
        $waktuOperasional = $jamMulai . ' - ' . $jamSelesai . ' WIB';

        // Membuat Kode Transaksi Acak Unik (Contoh: JPNS-866 seperti di screenshot kamu)
        $kodeInvoice = 'JPNS-' . rand(100, 999);

        // Mengambil pilihan metode pembayaran dari form request, default ke 'Tunai 💵' jika kosong
        $metodeBayar = $request->input('metode_pembayaran', 'Tunai 💵');

        // Menentukan status text awal agar sinkron dengan badge di riwayat.blade.php
        // Jika metode adalah Tunai, langsung 'Sudah Masuk', jika transfer/qris maka 'Menunggu Pembayaran'
        $statusAwal = (str_contains(strtolower($metodeBayar), 'tunai')) ? 'Sudah Masuk' : 'Menunggu Pembayaran';

        // Buat data Reservasi baru ke database dengan mapping kolom yang sinkron dengan view riwayat
        $reservasi = Reservasi::create([
            'kode_transaksi'   => $kodeInvoice,
            'user_id'          => Auth::check() ? Auth::id() : 1, // Menggunakan ID user login, fallback ke 1 untuk testing
            'nama_customer'    => Auth::check() ? Auth::user()->name : 'DIO', // Menggunakan nama user login, atau dummy 'DIO'
            'id_lapangan'      => $request->id_lapangan,
            'lapangan'         => $lapangan->nama_lapangan ?? 'Futsal GOR Jopan',
            'id_jadwal'        => $request->id_jadwal,
            
            // Kolom tanggal & waktu sesuai property yang dipanggil di riwayat.blade.php
            'tanggal_booking'  => $request->tanggal_booking,
            'tanggal_main'     => date('d Jun Y', strtotime($request->tanggal_booking)),
            'jam_operasional'  => $waktuOperasional,
            
            'metode_pembayaran'=> $metodeBayar,
            'total_harga'      => $lapangan->harga_per_jam, 
            'total_payar'      => $lapangan->harga_per_jam, // Menyediakan fallback jika view membaca total_payar
            'status_reservasi' => $statusAwal,
            'status'           => $statusAwal // Menyediakan properti status langsung untuk dibaca view
        ]);

        // Alihkan halaman ke route pembayaran dengan query string agar halaman nota sukses (Gambar ke-2) terisi otomatis
        return redirect()->route('pembayaran.index', $reservasi->kode_transaksi)
                         ->with([
                             'success' => 'Reservasi berhasil dibuat!',
                             'lapangan' => $reservasi->lapangan,
                             'tanggal' => $reservasi->tanggal_main,
                             'jam' => $reservasi->jam_operasional,
                             'total' => $reservasi->total_harga
                         ]);
    }
}