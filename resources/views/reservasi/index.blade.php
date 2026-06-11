<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Reservasi - {{ $lapangan->nama_lapangan }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-10">

    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-2 text-gray-800">{{ $lapangan->nama_lapangan }}</h2>
        <p class="text-sm text-gray-600 mb-4">{{ $lapangan->deskripsi }}</p>
        <p class="text-green-600 font-semibold mb-6">Harga: Rp {{ number_format($lapangan->harga_per_jam, 0, ',', '.') }} / jam</p>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('reservasi.store') }}" method="POST">
            @csrf 
            <input type="hidden" name="id_lapangan" value="{{ $lapangan->id_lapangan }}">

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Tanggal Main</label>
                <input type="date" name="tanggal_booking" required
                       min="{{ date('Y-m-d') }}" 
                       class="w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Jam Operasional</label>
                <select name="id_jadwal" required class="w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Pilih Jam --</option>
                    @foreach($jadwal as $j)
                        <option value="{{ $j->id_jadwal }}">
                            {{ date('H:i', strtotime($j->jam_mulai)) }} - {{ date('H:i', strtotime($j->jam_selesai)) }} WIB
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                <select name="metode_pembayaran" required class="w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Pilih Metode Pembayaran --</option>
                    <option value="Transfer Bank 🏦 (BCA)">Transfer Bank 🏦 (BCA)</option>
                    <option value="Transfer Bank 🏦 (Mandiri)">Transfer Bank 🏦 (Mandiri)</option>
                    <option value="E-Wallet 📱 (Dana/Gopay)">E-Wallet 📱 (Dana/Gopay)</option>
                    <option value="Tunai 💵 (Bayar di Tempat)">Tunai 💵 (Bayar di Tempat)</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md transition duration-200">
                Booking Sekarang
            </button>
        </form>
        
    </div>

</body>
</html>