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
        // Nama tabel diubah dari 'lapangans' menjadi 'lapangan'
        Schema::create('lapangan', function (Blueprint $table) {
            $table->id('id_lapangan'); // Primary Key kustom: id_lapangan
            $table->string('nama_lapangan');
            $table->string('jenis_olahraga'); // Contoh: Futsal, Badminton, Basket
            $table->decimal('harga_per_jam', 10, 2);
            $table->text('deskripsi')->nullable(); // Kolom deskripsi, boleh kosong
            $table->enum('status', ['tersedia', 'maintenance'])->default('tersedia'); // Status lapangan
            $table->timestamps(); // Otomatis membuat kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Disesuaikan untuk menghapus tabel 'lapangan' jika di-rollback
        Schema::dropIfExists('lapangan');
    }
};