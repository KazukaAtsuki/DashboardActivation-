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
    Schema::create('logger_activations', function (Blueprint $table) {
        $table->id();
        $table->string('logger_id')->unique(); // ID Unik Logger (misal: LOG-001)
        $table->string('logger_name');         // Nama Logger (misal: Logger Cerobong 1)
        $table->string('token')->unique();     // Token Rahasia
        $table->string('activation_code');     // Kode Aktivasi
        $table->string('status')->default('Active'); // Active/Inactive
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logger_activations');
    }
};
