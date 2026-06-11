<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id('id_pembayaran'); // Primary Key kustom: id_pembayaran
            
            // Foreign Key yang menghubungkan ke kolom id_reservasi di tabel reservasi
            $table->foreignId('id_reservasi')
                  ->constrained('reservasi', 'id_reservasi')
                  ->onDelete('cascade'); // Jika reservasi dihapus, data pembayaran otomatis ikut terhapus
            
            // Metode Pembayaran
            $table->enum('metode_pembayaran', ['transfer_bank', 'e-wallet', 'cash']);
            
            // Nominal yang dibayarkan
            $table->decimal('jumlah_bayar', 10, 2);
            
            // File gambar bukti transfer (boleh kosong jika bayar cash)
            $table->string('bukti_transfer')->nullable();
            
            // Status verifikasi pembayaran
            $table->enum('status_pembayaran', ['pending', 'lunas', 'gagal'])->default('pending');
            
            $table->timestamps(); // Membuat kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};