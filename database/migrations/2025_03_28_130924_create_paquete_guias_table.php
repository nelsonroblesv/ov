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
        Schema::create('paquete_guias', function (Blueprint $table) {
            $table->id();
            $table->string('periodo');
            $table->string('semana');
            $table->foreignId('zonas_id')->constrained()->onDelete('cascade');
            $table->foreignId('regiones_id')->constrained()->onDelete('cascade');
            $table->enum('estado', ['pendiente', 'completado'])->default('pendiente');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paquete_guias');
    }
};
