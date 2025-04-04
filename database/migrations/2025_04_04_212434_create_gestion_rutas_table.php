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
        Schema::create('gestion_rutas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('dia_semana');
            $table->string('tipo_semana');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->integer('orden')->nullable(); // Para definir el orden de visita (opcional)
            $table->timestamps();

            $table->unique(['user_id', 'dia_semana', 'tipo_semana', 'customer_id'], 'unique_ruta_cliente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gestion_rutas');
    }
};
