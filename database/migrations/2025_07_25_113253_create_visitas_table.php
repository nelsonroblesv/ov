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
            $table->foreignId('pedido_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained(); // vendedor que realiza la visita
            $table->date('fecha_visita');
            $table->enum('tipo_visita', ['EN', 'SE', 'SV']);
            $table->text('notas')->nullable();
            $table->json('evidencias')->nullable(); // foto de entrega, firma, etc.
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
