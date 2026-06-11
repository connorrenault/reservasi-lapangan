<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservasiController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes - JopanSport (VERSI UPDATE REDIRECT KE JADWAL SETELAH BAYAR)
|--------------------------------------------------------------------------
*/

// 1. Halaman utama / welcome bawaan Laravel
Route::get('/', function () {
    return view('welcome');
});

// 2. Route untuk Dashboard dengan pengaman data (class_exists)
Route::get('/dashboard', function () {
    $fields = collect([]);

    if (class_exists('\App\Models\Lapangan')) {
        try {
            $fields = \App\Models\Lapangan::all();
        } catch (\Exception $e) {
            $fields = collect([]);
        }
    } 

    if ($fields->isEmpty()) {
        $fields = [
            (object)[
                'id' => 1,
                'id_lapangan' => 1,
                'name' => 'Futsal GOR Jopan', 
                'nama_lapangan' => 'Futsal GOR Jopan', 
                'description' => 'Lapangan futsal menggunakan rumput sintetis premium.',
                'price_per_hour' => 150000,
                'harga_per_jam' => 150000,
                'type' => 'Futsal',
                'bookings' => collect([])
            ],
            (object)[
                'id' => 2,
                'id_lapangan' => 2,
                'name' => 'Badminton Arena 1', 
                'nama_lapangan' => 'Badminton Arena 1', 
                'description' => 'Lapangan badminton dengan karpet standar internasional.',
                'price_per_hour' => 60000,
                'harga_per_jam' => 60000,
                'type' => 'Badminton',
                'bookings' => collect([])
            ]
        ];
    }

    return view('dashboard', compact('fields')); 
})->name('dashboard');

// 3. Route Form Reservasi Lapangan
Route::get('/field/{id_lapangan}', function ($id_lapangan) {
    if (class_exists('\App\Http\Controllers\ReservasiController')) {
        try {
            return app(ReservasiController::class)->index($id_lapangan);
        } catch (\Exception $e) {
            // Jalur penyelamat jika database/controller belum siap
        }
    }

    $lapangan = (object)[
        'id' => $id_lapangan,
        'id_lapangan' => $id_lapangan, 
        'name' => $id_lapangan == 1 ? 'Futsal GOR Jopan' : 'Badminton Arena 1',
        'nama_lapangan' => $id_lapangan == 1 ? 'Futsal GOR Jopan' : 'Badminton Arena 1',
        'harga_per_jam' => $id_lapangan == 1 ? 150000 : 60000,
        'price_per_hour' => $id_lapangan == 1 ? 150000 : 60000,
        'deskripsi' => 'Lapangan olahraga berkualitas premium dengan fasilitas lengkap.',
        'type' => $id_lapangan == 1 ? 'Futsal' : 'Badminton'
    ];

    $jadwal = [
        (object)['id_jadwal' => 1, 'jam_mulai' => '08:00:00', 'jam_selesai' => '10:00:00'],
        (object)['id_jadwal' => 2, 'jam_mulai' => '10:00:00', 'jam_selesai' => '12:00:00'],
        (object)['id_jadwal' => 3, 'jam_mulai' => '13:00:00', 'jam_selesai' => '15:00:00'],
        (object)['id_jadwal' => 4, 'jam_mulai' => '16:00:00', 'jam_selesai' => '18:00:00'],
        (object)['id_jadwal' => 5, 'jam_mulai' => '19:00:00', 'jam_selesai' => '21:00:00'],
    ];
    
    return view('reservasi.index', compact('lapangan', 'jadwal'));
})->name('field.show');

// 4. Route Jadwal Lapangan dengan Logika Tampilan Real-time
Route::get('/jadwal', function () {
    $tanggal_pilihan = request('date', date('Y-m-d'));
    $fields = collect([]);

    if (class_exists('\App\Models\Lapangan')) {
        try {
            $fields = \App\Models\Lapangan::all();
        } catch (\Exception $e) {
            $fields = collect([]);
        }
    }

    if ($fields->isEmpty()) {
        $status_putra = session('payment_status_JPNS-219', 'Menunggu Pembayaran');
        
        $semua_transaksi = [];

        // Menampilkan data statis bawaan hanya jika belum dihapus lewat session 'cleared_static'
        $cleared_static = session('cleared_static', []);

        if (!in_array('DIO', $cleared_static)) {
            $semua_transaksi[] = [
                'id_booking' => 'DIO',
                'tanggal' => date('Y-m-d'), 
                'lapangan_id' => 1, 
                'jam' => '08:00 - 10:00 WIB',
                'customer' => 'DIO',
                'status' => 'Sudah Masuk'
            ];
        }
        if (!in_array('JOPAN', $cleared_static)) {
            $semua_transaksi[] = [
                'id_booking' => 'JOPAN',
                'tanggal' => date('Y-m-d', strtotime('+1 day')), 
                'lapangan_id' => 2, 
                'jam' => '16:00 - 18:00 WIB',
                'customer' => 'JOPAN',
                'status' => 'Sudah Masuk'
            ];
        }
        if (!in_array('PUTRA', $cleared_static) && $status_putra === 'Sudah Masuk') {
            $semua_transaksi[] = [
                'id_booking' => 'PUTRA',
                'tanggal' => date('Y-m-d'), 
                'lapangan_id' => 1, 
                'jam' => '19:00 - 21:00 WIB',
                'customer' => 'PUTRA',
                'status' => $status_putra
            ];
        }

        $custom_bookings = session('custom_bookings', []);
        foreach ($custom_bookings as $key => $cb) {
            // Abaikan booking yang statusnya sudah diselesaikan oleh aksi tombol admin
            if (isset($cb['status_antrian']) && $cb['status_antrian'] === 'selesai') {
                continue;
            }

            $tgl_mentah = isset($cb['tanggal_mentah']) ? $cb['tanggal_mentah'] : date('Y-m-d', strtotime($cb['tanggal_main']));
            $lap_id = (strpos(strtolower($cb['lapangan']), 'futsal') !== false) ? 1 : 2;
            
            $semua_transaksi[] = [
                'id_booking' => $key,
                'tanggal' => $tgl_mentah,
                'lapangan_id' => $lap_id,
                'jam' => $cb['jam_operasional'],
                'customer' => $cb['nama_customer'],
                'status' => $cb['status']
            ];
        }

        $bookings_futsal = collect([]);
        $bookings_badminton = collect([]);

        foreach ($semua_transaksi as $t) {
            if ($t['tanggal'] === $tanggal_pilihan && $t['status'] === 'Sudah Masuk') {
                
                $jam_bersih = str_replace(' WIB', '', $t['jam']);
                $pecah_jam = explode(' - ', $jam_bersih);
                $start = isset($pecah_jam[0]) ? trim($pecah_jam[0]) . ':00' : '08:00:00';
                $end = isset($pecah_jam[1]) ? trim($pecah_jam[1]) . ':00' : '10:00:00';

                $booking_obj = (object)[
                    'id_booking' => $t['id_booking'],
                    'start_time' => $start,
                    'end_time' => $end,
                    'status' => 'selesai', // Menggunakan huruf kecil murni agar CSS warna hijau menyala penuh
                    'nama_pemesan' => $t['customer'],
                    'user' => (object)['name' => $t['customer']]
                ];

                if ($t['lapangan_id'] === 1) {
                    $bookings_futsal->push($booking_obj);
                } else {
                    $bookings_badminton->push($booking_obj);
                }
            }
        }

        $fields = [
            (object)[
                'id' => 1,
                'name' => 'Futsal GOR Jopan',
                'description' => 'Lapangan futsal menggunakan rumput sintetis premium.',
                'price_per_hour' => 150000,
                'type' => 'Futsal',
                'bookings' => $bookings_futsal 
            ],
            (object)[
                'id' => 2,
                'name' => 'Badminton Arena 1',
                'description' => 'Lapangan badminton dengan karpet standar internasional.',
                'price_per_hour' => 60000,
                'type' => 'Badminton',
                'bookings' => $bookings_badminton 
            ]
        ];
    }

    return view('jadwal', compact('tanggal_pilihan', 'fields'));
})->name('jadwal');


// 📌 ROUTE: Aksi Klik Tombol Menyelesaikan Antrean di Jadwal
Route::get('/jadwal/update-antrian/{id}', function ($id) {
    if (class_exists('\App\Http\Controllers\ReservasiController')) {
        try {
            if (method_exists(\App\Http\Controllers\ReservasiController::class, 'finishQueue')) {
                return app(ReservasiController::class)->finishQueue($id);
            }
        } catch (\Exception $e) { }
    }

    if (in_array($id, ['DIO', 'JOPAN', 'PUTRA'])) {
        $cleared = session('cleared_static', []);
        $cleared[] = $id;
        session(['cleared_static' => $cleared]);
    } else {
        $custom_bookings = session('custom_bookings', []);
        if (isset($custom_bookings[$id])) {
            $custom_bookings[$id]['status_antrian'] = 'selesai';
            session(['custom_bookings' => $custom_bookings]);
        }
    }

    return redirect()->route('jadwal')->with('success_alert', 'Slot antrean waktu berhasil diselesaikan dan dikosongkan!');
});


// 5. Route Proses Booking Awal (MODIFIKASI: NAMA CUSTOMER OTOMATIS LOGIN USER)
Route::post('/booking', function () {
    if (class_exists('\App\Http\Controllers\ReservasiController')) {
        try {
            return app(ReservasiController::class)->store(request());
        } catch (\Exception $e) {
            // Lanjut ke fallback
        }
    }

    $id_lapangan = request('id_lapangan', 1);
    $tanggal = request('tanggal_booking', date('Y-m-d'));
    $id_jadwal = request('id_jadwal', 1);
    $metode_pilihan = request('metode_pembayaran', 'Tunai 💵');

    $list_jam = [
        1 => '08:00 - 10:00 WIB',
        2 => '10:00 - 12:00 WIB',
        3 => '13:00 - 15:00 WIB',
        4 => '16:00 - 18:00 WIB',
        5 => '19:00 - 21:00 WIB'
    ];
    $jam_pilihan = $list_jam[$id_jadwal] ?? '08:00 - 10:00 WIB';
    $nama_lapangan = $id_lapangan == 1 ? 'Futsal GOR Jopan' : 'Badminton Arena 1';
    $total_harga = $id_lapangan == 1 ? 150000 : 60000;

    $invoice_baru = 'JPNS-' . rand(100, 999);

    // --- LOGIKA BARU DI SINI ---
    // Jika user login, ambil namanya. Jika tidak ada yang login, gunakan 'Pelanggan Jopan'
    $nama_customer_aktif = Auth::check() ? Auth::user()->name : 'Pelanggan Jopan';

    $riwayat_session = session('custom_bookings', []);
    $riwayat_session[$invoice_baru] = [
        'code_transaksi' => $invoice_baru, // Dukungan jika dipanggil key code
        'kode_transaksi' => $invoice_baru,
        'nama_customer' => $nama_customer_aktif, // <-- SEKARANG SUDAH DINAMIS
        'tanggal_main' => date('d M Y', strtotime($tanggal)),
        'tanggal_mentah' => $tanggal, 
        'jam_operasional' => $jam_pilihan,
        'lapangan' => $nama_lapangan,
        'metode_pembayaran' => $metode_pilihan,
        'total_payar' => $total_harga,
        'status' => 'Sudah Masuk',
        'status_antrian' => 'aktif'
    ];
    
    session(['custom_bookings' => $riwayat_session]);

    return redirect()->route('pembayaran.index', [
        'id' => $invoice_baru,
        'lapangan' => $nama_lapangan,
        'tanggal' => $tanggal,
        'jam' => $jam_pilihan,
        'total' => 'Rp ' . number_format($total_harga, 0, ',', '.'),
        'metode' => $metode_pilihan
    ]);
})->name('reservasi.store');


// 📌 ROUTE MODIFIKASI: Mengonfirmasi status & langsung lempar ke halaman Jadwal
Route::post('/booking/complete/{id}', function ($id) {
    // Jalankan penyimpanan status di session
    session(['payment_status_' . $id => 'Sudah Masuk']);

    // Cari tahu apakah ID ini milik bookingan baru (custom_bookings) untuk mencocokkan tanggal filter jadwalnya
    $custom_bookings = session('custom_bookings', []);
    $tanggal_target = date('Y-m-d'); // Default ke hari ini jika tidak ketemu

    if (isset($custom_bookings[$id]['tanggal_mentah'])) {
        $tanggal_target = $custom_bookings[$id]['tanggal_mentah'];
    }

    // Alihkan pengguna secara langsung ke halaman jadwal lapangan dengan filter tanggal otomatis
    return redirect()->route('jadwal', ['date' => $tanggal_target])->with('success_alert', 'Demo Pembayaran Berhasil Terkonfirmasi! Silakan periksa jam main kamu.');
})->name('booking.complete');


// 6. Route Riwayat Booking Navbar
Route::get('/booking/{id_lapangan?}', function ($id_lapangan = 1) {
    if (class_exists('\App\Http\Controllers\ReservasiController')) {
        try {
            return app(ReservasiController::class)->index($id_lapangan);
        } catch (\Exception $e) {
            // Jalur penyelamat jika controller asli error
        }
    }

    $status_putra = session('payment_status_JPNS-219', 'Menunggu Pembayaran');

    $riwayat_booking = [
        (object)[
            'kode_transaksi' => 'JPNS-412',
            'nama_customer' => 'DIO',
            'tanggal_main' => date('d M Y'),
            'jam_operasional' => '08:00 - 10:00',
            'lapangan' => 'Futsal GOR Jopan',
            'metode_pembayaran' => 'Transfer Bank 🏦 (BCA)',
            'total_payar' => 150000,
            'status' => 'Sudah Masuk'
        ],
        (object)[
            'kode_transaksi' => 'JPNS-884',
            'nama_customer' => 'JOPAN',
            'tanggal_main' => date('d M Y', strtotime('+1 day')),
            'jam_operasional' => '16:00 - 18:00',
            'lapangan' => 'Badminton Arena 1',
            'metode_pembayaran' => 'Tunai 💵',
            'total_payar' => 60000,
            'status' => 'Sudah Masuk'
        ],
        (object)[
            'kode_transaksi' => 'JPNS-219',
            'nama_customer' => 'PUTRA',
            'tanggal_main' => date('d M Y'),
            'jam_operasional' => '19:00 - 21:00',
            'lapangan' => 'Futsal GOR Jopan',
            'metode_pembayaran' => 'QRIS / E-Wallet 📱',
            'total_payar' => 150000,
            'status' => $status_putra
        ],
    ];

    $bookingan_baru_user = session('custom_bookings', []);
    foreach ($bookingan_baru_user as $item) {
        $riwayat_booking[] = (object) $item;
    }

    return view('riwayat', compact('riwayat_booking'));
})->name('booking.index');

// Route logout
Route::post('/logout', function () {
    Auth::logout(); // Pastikan proses logout session asli berjalan
    return redirect('/'); 
})->name('logout');

// 7. Halaman Detail Pembayaran Sukses
Route::get('/pembayaran/{id}', function ($id) {
    $lapangan = request('lapangan', 'Futsal GOR Jopan');
    $tanggal = request('tanggal', date('Y-m-d'));
    $jam = request('jam', '08:00 - 10:00 WIB');
    $total = request('total', 'Rp 150.000');
    $metode = request('metode', 'Tunai 💵');

    $tanggal_rapi = date('d M Y', strtotime($tanggal));

    return "
    <div style='font-family: sans-serif; text-align: center; padding-top: 60px; min-height: 100vh; background-color: #0f172a; color: #f8fafc; margin: 0;'>
        <div style='max-width: 520px; margin: 0 auto; padding: 30px; border: 1px solid #1e293b; background-color: #1e293b; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3);'>
            <h2 style='color: #22c55e; margin-bottom: 5px;'>🎉 Booking Berhasil Diproses!</h2>
            <p style='color: #94a3b8; margin-top: 0; margin-bottom: 20px;'>Kode Invoice: <strong style='color: #eab308; font-family: monospace; font-size: 18px;'>" . $id . "</strong></p>
            
            <div style='text-align: left; background: #0f172a; padding: 20px; border-radius: 12px; margin-bottom: 25px; border: 1px solid #334155;'>
                <h4 style='color: #3b82f6; margin-top: 0; margin-bottom: 15px; border-bottom: 1px dashed #334155; padding-bottom: 8px;'>📋 Rincian Transaksi Reservasi:</h4>
                <p style='margin: 8px 0; font-size: 14px; color: #cbd5e1;'><strong>Nama Lapangan:</strong> <span style='float: right; color: #fff;'>" . $lapangan . "</span></p>
                <p style='margin: 8px 0; font-size: 14px; color: #cbd5e1;'><strong>Tanggal Main:</strong> <span style='float: right; color: #fff;'>" . $tanggal_rapi . "</span></p>
                <p style='margin: 8px 0; font-size: 14px; color: #cbd5e1;'><strong>Jam Operasional:</strong> <span style='float: right; color: #fff;'>" . $jam . "</span></p>
                <p style='margin: 8px 0; font-size: 14px; color: #cbd5e1;'><strong>Metode Bayar:</strong> <span style='float: right; color: #eab308; font-weight: bold;'>" . $metode . "</span></p>
                <p style='margin: 15px 0 0 0; font-size: 16px; color: #cbd5e1; padding-top: 10px; border-top: 1px dashed #334155;'><strong>Total Biaya:</strong> <span style='float: right; color: #22c55e; font-weight: bold;'>" . $total . "</span></p>
            </div>

            <div style='background: #1a2236; padding: 12px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #2e3d5c; font-size: 14px; color: #22c55e;'>
                Status: <strong>Tersimpan Otomatis di Riwayat ✔️</strong>
            </div>
            
            <a href='".route('booking.index')."' style='display: inline-block; padding: 12px 24px; background-color: #22c55e; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; width: 85%; margin-bottom: 12px;'>📋 Pergi ke Riwayat Booking</a>
            <br>
            <a href='".route('jadwal', ['date' => $tanggal])."' style='display: inline-block; padding: 12px 24px; background-color: #3b82f6; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; width: 85;'>📅 Cek Jadwal Lapangan</a>
        </div>
    </div>
    ";
})->name('pembayaran.index');