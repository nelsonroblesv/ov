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
        Schema::create('entrega_cobranzas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alta_user_id')->constrained('users');
            $table->integer('periodo');
            $table->enum('semana_mes', ['1', '2', '3', '4'])->default(1);
            $table->integer('semana_anio');
            $table->boolean('tipo_semana', [0, 1])->default(0);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrega_cobranzas');
    }
};
