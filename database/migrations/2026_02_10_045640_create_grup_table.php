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
        Schema::create('grup', function (Blueprint $table) {
            $table->id('id_grup');
            $table->foreignId('id_user')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('jam',);
            $table->string('kamar', 30);
            $table->decimal('berat');
            $table->string('jenis_pakaian', 30);
            $table->integer('jumlah_orang');
            $table->string('status_data', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grup');
    }
};
