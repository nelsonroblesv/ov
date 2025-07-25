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
        Schema::create('visitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedidos_id')->constrained()->cascadeOnDelete();
            $table->foreignId('users_id')->constrained(); // vendedor que realiza la visita
            $table->date('fecha_visita');
            $table->enum('tipo_visita', ['entrega', 'seguimiento', 'siguiente visita']);
            $table->text('observaciones')->nullable();
            $table->string('evidencias')->nullable(); // foto de entrega, firma, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitas');
    }
};
