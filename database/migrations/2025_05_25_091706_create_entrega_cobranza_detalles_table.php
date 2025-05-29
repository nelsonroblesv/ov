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
        Schema::create('entrega_cobranza_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrega_cobranza_id')->constrained();
            $table->date('fecha_programada');
            $table->enum('tipo_visita', ['PO', 'PR', 'EP', 'ER', 'CO'])->default('ER');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('customer_id')->constrained('customers');
            $table->boolean('status', [0, 1])->default(0);
            $table->date('fecha_visita')->nullable();
            $table->boolean('is_verified', [0, 1])->default(0);
            $table->string('notas_admin')->nullable();
            $table->string('notas_colab')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrega_cobranza_detalles');
    }
};
