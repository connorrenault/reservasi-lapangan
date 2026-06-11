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
        Schema::create('reservasi', function (Blueprint $table) {
            $table->id('id_reservasi'); // Primary Key menggunakan id_reservasi
            
            // Foreign Key ke tabel users (menggunakan user_id karena tabel users memakai id bawaan Laravel)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Foreign Key ke tabel lapangan
            $table->foreignId('id_lapangan')->constrained('lapangan', 'id_lapangan')->onDelete('cascade');
            
            // Foreign Key ke tabel jadwal
            $table->foreignId('id_jadwal')->constrained('jadwal', 'id_jadwal')->onDelete('cascade');
            
            $table->date('tanggal_booking'); // Tanggal pelaksanaan main/sewa
            $table->decimal('total_harga', 10, 2); // Total biaya sewa
            
            // Status alur reservasi
            $table->enum('status_reservasi', [
                'menunggu_pembayaran', 
                'dikonfirmasi', 
                'dibatalkan', 
                'selesai'
            ])->default('menunggu_pembayaran');
            
            $table->timestamps(); // Membuat kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservasi');
    }
};