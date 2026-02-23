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
        Schema::create('detail_laundry', function (Blueprint $table) {
            $table->id('id_detail');
            $table->foreignId('id_grup')->constrained('grup', 'id_grup')->cascadeOnDelete();
            $table->foreignId('id_pelanggan')->constrained('pelanggan', 'id_pelanggan')->cascadeOnDelete();
            $table->integer('baju');
            $table->integer('rok');
            $table->integer('jilbab');
            $table->integer('kaos');
            $table->string('keterangan',100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_laundry');
    }
};
