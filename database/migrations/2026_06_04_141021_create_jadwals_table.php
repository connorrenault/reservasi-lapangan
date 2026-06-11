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
        // Nama tabel diubah dari 'jadwals' menjadi 'jadwal'
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id('id_jadwal'); // Primary Key kustom: id_jadwal
            
            // Menghubungkan Foreign Key ke kolom id_lapangan di tabel lapangan
            $table->foreignId('id_lapangan')
                  ->constrained('lapangan', 'id_lapangan')
                  ->onDelete('cascade'); // Jika lapangan dihapus, jadwalnya ikut terhapus
            
            $table->time('jam_mulai'); // Contoh input: 09:00:00
            $table->time('jam_selesai'); // Contoh input: 10:00:00
            $table->timestamps(); // Membuat kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Disesuaikan untuk menghapus tabel 'jadwal' saat di-rollback
        Schema::dropIfExists('jadwal');
    }
};