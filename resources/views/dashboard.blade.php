<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Booking Lapangan - JopanSport</title>
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
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.4);
            background: rgba(255, 255, 255, 0.15);
        }
        .navbar-glass {
            background: rgba(25, 135, 84, 0.85);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .text-muted-custom {
            color: #bdc3c7;
        }
        .card-img-container {
            height: 220px;
            overflow: hidden;
            position: relative;
        }
        .card-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .search-input {
            background: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: #fff !important;
        }
        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        .search-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25) !important;
            border-color: #ffc107 !important;
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
                    <a class="nav-link active fw-bold text-warning" href="{{ route('dashboard') }}">Dashboard</a>
                    <a class="nav-link fw-semibold" href="{{ route('jadwal') }}">Jadwal Lapangan</a>
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

    <div class="container py-3">
        <div class="row mb-4 text-center text-md-start align-items-center">
            <div class="col-md-7 mb-4 mb-md-0">
                <h1 class="fw-bold display-4 mb-2" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">Selamat Datang di JopanSport 👋</h1>
                <p class="fs-5 text-muted-custom mb-0">Silakan pilih lapangan favoritmu dan lakukan pemesanan dengan mudah.</p>
            </div>
            
            <div class="col-md-5">
                <div class="p-3 rounded glass-card shadow-sm">
                    <div class="input-group mb-2">
                        <input type="text" id="searchInput" class="form-control search-input" placeholder="Cari nama lapangan..." onkeyup="filterFields()">
                    </div>
                    <div class="d-flex gap-2 justify-content-md-start justify-content-center">
                        <button class="btn btn-sm btn-warning fw-bold px-3 text-dark filter-btn active" onclick="filterCategory('all', this)">Semua</button>
                        <button class="btn btn-sm btn-outline-light fw-bold px-3 filter-btn" onclick="filterCategory('futsal', this)">Futsal</button>
                        <button class="btn btn-sm btn-outline-light fw-bold px-3 filter-btn" onclick="filterCategory('badminton', this)">Badminton</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="noFieldsAlert" class="text-center py-5 d-none">
            <span class="fs-1 d-block mb-3">🔍❌</span>
            <h4 class="fw-bold text-warning">Lapangan Tidak Ditemukan</h4>
            <p class="text-white-50">Maaf, kata kunci atau kategori lapangan yang kamu pilih sedang tidak tersedia.</p>
        </div>

        <div class="row" id="fieldsContainer">
            @foreach($fields as $field)
                <div class="col-md-6 mb-4 field-item" data-name="{{ strtolower($field->name) }}">
                    <div class="card h-100 glass-card text-white shadow overflow-hidden border-0">
                        
                        <div class="card-img-container">
                            @if(Str::contains(strtolower($field->name), 'futsal'))
                                <img src="{{ asset('images/futsal.jpg') }}" alt="Lapangan Futsal">
                            @elseif(Str::contains(strtolower($field->name), 'badminton'))
                                <img src="{{ asset('images/badminton.jpg') }}" alt="Lapangan Badminton">
                            @else
                                <img src="{{ asset('images/default-sports.jpg') }}" alt="Arena Olahraga">
                            @endif

                            <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-3 px-3 py-2 fw-bold text-uppercase shadow">
                                {{ $field->type ?? 'Sport' }}
                            </span>
                        </div>

                        <div class="card-body d-flex flex-column p-4">
                            <h3 class="card-title fw-bold text-warning mb-3 field-title">{{ $field->name }}</h3>
                            <p class="card-text text-muted-custom flex-grow-1">{{ $field->description }}</p>
                            
                            <hr class="border-light opacity-25 mb-4">
                            
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <div>
                                    <span class="fs-4 fw-bold text-white">Rp {{ number_format($field->price_per_hour, 0, ',', '.') }}</span>
                                    <small class="text-muted-custom d-block">/ jam</small>
                                </div>
                                <a href="{{ route('field.show', $field->id) }}" class="btn btn-warning btn-lg px-4 fw-bold text-dark shadow-sm">Pesan Sekarang</a>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        let currentCategory = 'all';

        function filterFields() {
            const searchKeyword = document.getElementById('searchInput').value.toLowerCase();
            const items = document.querySelectorAll('.field-item');
            const alertBox = document.getElementById('noFieldsAlert');
            let visibleCount = 0;

            items.forEach(item => {
                const name = item.getAttribute('data-name');
                const matchesSearch = name.includes(searchKeyword);
                const matchesCategory = (currentCategory === 'all' || name.includes(currentCategory));

                if (matchesSearch && matchesCategory) {
                    item.style.display = 'block';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            // MODIFIKASI: Logika untuk mematikan/menghidupkan status banner "Tidak Ditemukan"
            if (visibleCount === 0) {
                alertBox.classList.remove('d-none');
            } else {
                alertBox.classList.add('d-none');
            }
        }

        function filterCategory(category, button) {
            currentCategory = category;
            
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('btn-warning', 'text-dark');
                btn.classList.add('btn-outline-light');
            });
            button.classList.remove('btn-outline-light');
            button.classList.add('btn-warning', 'text-dark');

            filterFields();
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>