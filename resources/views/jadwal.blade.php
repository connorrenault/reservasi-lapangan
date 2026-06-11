<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Penggunaan Lapangan - JopanSport</title>
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
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .glass-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }
        .navbar-glass {
            background: rgba(25, 135, 84, 0.85);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .time-slot {
            background: rgba(255, 255, 255, 0.06);
            border-left: 4px solid #ced4da;
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 10px;
            transition: all 0.2s ease;
        }
        .time-slot:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: scale(1.01);
        }
        .time-slot.status-selesai {
            border-left-color: #198754; /* Hijau sukses */
            background: rgba(25, 135, 84, 0.15) !important;
        }
        .time-slot.status-selesai:hover {
            background: rgba(25, 135, 84, 0.2) !important;
        }
        .time-slot.status-pending {
            border-left-color: #ffc107; /* Kuning warning */
            background: rgba(255, 193, 7, 0.08);
        }
        .time-slot.status-pending:hover {
            background: rgba(255, 193, 7, 0.12);
        }
        .date-picker-custom {
            background: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: #fff !important;
        }
        .date-picker-custom::-webkit-calendar-picker-indicator {
            filter: invert(1); /* Membuat ikon kalender menjadi putih */
            cursor: pointer;
        }
        /* Style Tambahan Efek Hover untuk Tombol Status Klik */
        .btn-status-klik {
            transition: all 0.2s ease-in-out;
            text-decoration: none !important;
        }
        .btn-status-klik:hover {
            transform: scale(1.05);
            opacity: 0.9;
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
                    <a class="nav-link active fw-bold text-warning" href="{{ route('jadwal') }}">Jadwal Lapangan</a>
                    <a class="nav-link fw-semibold" href="{{ route('booking.index') }}">Riwayat Booking</a>
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
        @if(session('success_alert'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow mb-4 text-dark fw-semibold" role="alert" style="background: #22c55e; color: #fff !important;">
                🎉 {{ session('success_alert') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row align-items-center mb-4 text-center text-md-start">
            <div class="col-md-7 mb-3 mb-md-0">
                <h2 class="fw-bold mb-1" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.4);">Jadwal Penggunaan Arena 🗓️</h2>
                <p class="text-white-50 mb-0">Cari tahu jam kosong biar mainnya ga tabrakan sama tim lain. Klik tombol status jika antrian sudah selesai.</p>
            </div>
            
            <div class="col-md-5">
                <form action="{{ route('jadwal') }}" method="GET" class="d-flex gap-2 justify-content-md-end justify-content-center p-2 rounded glass-card shadow-sm">
                    <input type="date" name="date" class="form-control w-auto date-picker-custom fw-semibold" value="{{ $tanggal_pilihan }}" onchange="this.form.submit()">
                    <button type="submit" class="btn btn-warning fw-bold text-dark px-4">Cari</button>
                </form>
            </div>
        </div>

        <div class="row">
            @foreach($fields as $field)
                <div class="col-md-6 mb-4">
                    <div class="card h-100 glass-card text-white shadow border-0">
                        <div class="card-header bg-transparent border-bottom border-light border-opacity-10 p-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="fw-bold text-warning mb-0">{{ $field->name }}</h4>
                                <small class="text-white-50">Tanggal: {{ date('d M Y', strtotime($tanggal_pilihan)) }}</small>
                            </div>
                            <span class="badge bg-light bg-opacity-10 text-white-50 border border-secondary border-opacity-25 text-uppercase fs-6">
                                {{ Str::contains(strtolower($field->name), 'futsal') ? '⚽ Futsal' : '🏸 Badminton' }}
                            </span>
                        </div>
                        <div class="card-body p-4">
                            
                            @if(count($field->bookings) > 0)
                                <p class="small text-white-50 mb-3 fw-semibold">📅 Jam yang sudah terisi (Klik tombol status untuk selesaikan):</p>
                                @foreach($field->bookings as $b)
                                    @php
                                        // Mengubah status menjadi huruf kecil dan menghapus spasi liar agar pengecekan akurat
                                        $checkStatus = strtolower(trim($b->status));
                                    @endphp
                                    
                                    <div class="time-slot {{ ($checkStatus == 'selesai' || $checkStatus == 'sudah masuk') ? 'status-selesai' : 'status-pending' }} d-flex justify-content-between align-items-center shadow-sm">
                                        <div>
                                            <span class="fw-bold d-block text-white" style="font-size: 1.1rem;">
                                                ⏰ {{ date('H:i', strtotime($b->start_time)) }} - {{ date('H:i', strtotime($b->end_time)) }} WIB
                                            </span>
                                            <small class="text-white-50">Oleh: <strong class="text-warning">{{ $b->nama_pemesan ?? 'Team Pelanggan' }}</strong></small>
                                        </div>
                                        
                                        <a href="{{ url('/jadwal/update-antrian/' . $b->id_booking) }}" 
                                           onclick="return confirm('Selesaikan antrean jam ini dan kosongkan slot untuk tim selanjutnya?')" 
                                           class="btn-status-klik" 
                                           title="Klik untuk menyelesaikan antrean jam ini">
                                            <span class="badge {{ ($checkStatus == 'selesai' || $checkStatus == 'sudah masuk') ? 'bg-success' : 'bg-warning text-dark' }} fw-bold text-uppercase px-3 py-2 shadow-sm" style="cursor: pointer;">
                                                ✔ {{ $b->status }}
                                            </span>
                                        </a>

                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-5">
                                    <span class="fs-1 d-block mb-2">🟢</span>
                                    <h5 class="fw-bold text-success mb-1">Lapangan Kosong Seharian</h5>
                                    <p class="mb-0 small text-warning">Bebas pilih jam booking sesukamu.</p>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-3 text-center text-md-start">
            <a href="{{ route('dashboard') }}" class="btn btn-warning btn-lg fw-bold text-dark px-4 shadow btn-hover-grow">Pesan Lapangan Sekarang 🚀</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>