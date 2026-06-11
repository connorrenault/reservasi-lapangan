<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Booking Anda - JopanSport</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            min-height: 100vh;
            color: #fff;
            font-family: 'Poppins', sans-serif;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .navbar-glass {
            background: rgba(25, 135, 84, 0.85);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .table-glass {
            color: #fff !important;
        }
        .table-glass th {
            background: rgba(255, 255, 255, 0.15) !important;
            color: #ffc107 !important;
            font-weight: bold;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2) !important;
        }
        .table-glass tbody tr {
            transition: background-color 0.2s ease;
        }
        .table-glass tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.04) !important;
        }
        .table-glass td {
            background: transparent !important;
            color: #fff !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        }
        .accordion-payment {
            --bs-accordion-bg: rgba(32, 58, 67, 0.95);
            --bs-accordion-color: #fff;
            --bs-accordion-btn-bg: rgba(255, 193, 7, 0.1);
            --bs-accordion-btn-color: #ffc107;
            --bs-accordion-active-bg: #ffc107;
            --bs-accordion-active-color: #000;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            overflow: hidden;
        }
        .pay-option {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            transition: all 0.2s ease;
        }
        .pay-option:hover {
            background: rgba(255, 193, 7, 0.15);
            border-color: #ffc107;
        }
        /* Style tambahan agar link tombol hijau rapi */
        .btn-status-link {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .btn-status-link:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
            color: #fff !important;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-glass sticky-top mb-4 shadow">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="{{ route('dashboard') }}">⚽ JopanSport</a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav me-auto mb-2 mb-lg-0">
                    <a class="nav-link fw-semibold" href="{{ route('dashboard') }}">Dashboard</a>
                    <a class="nav-link fw-semibold" href="{{ route('jadwal') }}">Jadwal Lapangan</a>
                    <a class="nav-link active fw-bold text-warning" href="{{ route('booking.index') }}">Riwayat Booking</a>
                </div>

                <div class="navbar-nav ms-auto align-items-center">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle btn btn-outline-light btn-sm px-3 text-white d-flex align-items-center gap-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 20px; background: rgba(255,255,255,0.05);">
                            <span>👤 {{ Auth::user()->name ?? 'Pelanggan' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="userDropdown" style="background: rgba(32, 58, 67, 0.95); backdrop-filter: blur(10px);">
                            <li>
                                <div class="dropdown-header text-white-50 small">Masuk Sebagai:</div>
                                <div class="px-3 py-1 fw-bold text-warning">{{ Auth::user()->email ?? 'user@jopansport.com' }}</div>
                            </li>
                            <li><hr class="dropdown-divider border-light opacity-25"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger fw-semibold d-flex align-items-center gap-2">
                                        🚪 Keluar / Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row mb-4">
            <div class="col">
                <h2 class="fw-bold mb-2" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.4);">Riwayat Pesanan Arena 📝</h2>
                <p class="text-white-50 mb-0">Periksa jadwal main dan status persetujuan pemesanan tempatmu di bawah ini.</p>
            </div>
        </div>

        <div class="card glass-card shadow border-0 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-glass align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="p-3 text-center" style="width: 70px;">No</th>
                                <th>Kode Transaksi</th>
                                <th>Nama Customer</th> 
                                <th>Nama Lapangan</th>
                                <th>Tanggal</th>
                                <th>Waktu Main</th>
                                <th>Metode</th> 
                                <th>Total Harga</th>
                                <th class="p-3 text-center" style="min-width: 260px;">Status / Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayat_booking as $index => $booking)
                                <tr>
                                    <td class="p-3 text-center fw-bold text-warning">{{ $index + 1 }}</td>
                                    <td class="font-monospace text-info fw-bold">{{ $booking->kode_transaksi ?? 'JPNS-772' }}</td>
                                    <td class="fw-bold text-white">{{ $booking->nama_customer ?? 'Pelanggan' }}</td>
                                    <td class="fw-bold">{{ $booking->field->name ?? $booking->lapangan }}</td>
                                    <td>{{ isset($booking->booking_date) ? date('d M Y', strtotime($booking->booking_date)) : $booking->tanggal_main }}</td>
                                    <td>
                                        {{ isset($booking->start_time) ? date('H:i', strtotime($booking->start_time)).' - '.date('H:i', strtotime($booking->end_time)) : $booking->jam_operasional }} WIB
                                    </td>
                                    <td>
                                        <span class="badge bg-dark border border-secondary text-light">
                                            {{ $booking->metode_pembayaran ?? 'Tunai 💵' }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-warning">
                                        Rp {{ isset($booking->total_price) ? number_format($booking->total_price, 0, ',', '.') : number_format($booking->total_payar, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3">
                                        @if($booking->status == 'Pending' || $booking->status == 'Menunggu Pembayaran')
                                            
                                            <div class="accordion accordion-payment" id="paymentAccordion{{ $index }}">
                                                <div class="accordion-item border-0">
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button collapsed fw-bold text-uppercase justify-content-center text-center w-100" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePay{{ $index }}" aria-expanded="false" style="font-size: 0.85rem;">
                                                            ⚠️ {{ $booking->status }} (Klik Bayar)
                                                        </button>
                                                    </h2>
                                                    <div id="collapsePay{{ $index }}" class="accordion-collapse collapse" data-bs-parent="#paymentAccordion{{ $index }}">
                                                        <div class="accordion-body p-3 text-start">
                                                            <p class="text-white-50 small mb-1">Total Tagihan:</p>
                                                            <h5 class="fw-bold text-warning mb-3">Rp {{ isset($booking->total_price) ? number_format($booking->total_price, 0, ',', '.') : number_format($booking->total_payar, 0, ',', '.') }}</h5>
                                                            
                                                            <div class="p-2 rounded mb-2 pay-option d-flex justify-content-between align-items-center">
                                                                <div style="font-size: 0.8rem;">
                                                                    <span class="fw-bold d-block text-white">Transfer BCA</span>
                                                                    <span class="text-white-50">123-4567-890 a/n JopanSport</span>
                                                                </div>
                                                                <span class="badge bg-light text-dark fw-bold" style="font-size: 0.65rem;">BCA</span>
                                                            </div>

                                                            <div class="p-2 rounded mb-2 pay-option d-flex justify-content-between align-items-center">
                                                                <div style="font-size: 0.8rem;">
                                                                    <span class="fw-bold d-block text-white">QRIS / E-Wallet</span>
                                                                    <span class="text-white-50">Scan otomatis di lokasi</span>
                                                                </div>
                                                                <span class="badge bg-danger text-white fw-bold" style="font-size: 0.65rem;">QRIS</span>
                                                            </div>

                                                            <form action="{{ route('booking.complete', $booking->kode_transaksi ?? 'JPNS-219') }}" method="POST" class="mt-3">
                                                                @csrf
                                                                <button type="submit" class="btn btn-warning btn-sm w-100 fw-bold text-dark py-2 rounded shadow-sm text-uppercase" style="font-size: 0.8rem;">
                                                                    Konfirmasi Sudah Bayar 🚀
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        @else
                                            @php
                                                // Deteksi tanggal mentah agar filter jadwal terbaca otomatis oleh route
                                                $target_date = isset($booking->booking_date) ? $booking->booking_date : (isset($booking->tanggal_mentah) ? $booking->tanggal_mentah : date('Y-m-d'));
                                            @endphp
                                            <div class="text-center">
                                                <a href="{{ route('jadwal', ['date' => $target_date]) }}" class="btn btn-sm btn-success text-white px-4 py-2 fw-bold text-uppercase shadow-sm btn-status-link text-decoration-none d-inline-block" title="Klik untuk langsung lihat Jadwal Lapangan tanggal ini">
                                                    ✅ {{ $booking->status }} 🗓️
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center p-5 text-white-50 fs-5">Belum ada riwayat pemesanan lapangan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="mt-4 text-center text-md-start">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-warning btn-lg fw-bold px-4 shadow-sm">← Kembali Pilih Lapangan</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @if(session('success_alert'))
        <script>
            alert("{{ session('success_alert') }}");
        </script>
    @endif
</body>
</html>