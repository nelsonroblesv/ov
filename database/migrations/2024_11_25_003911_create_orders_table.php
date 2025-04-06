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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('number')->unique();
            $table->enum('status', ['PEN', 'COM', 'REC', 'REU', 'DEV', 'SIG'])->default('PEN');
            $table->enum('tipo_nota', ['Sistema', 'Remisión'])->default('Sistema');
            $table->enum('tipo_semana_nota', ['PAR', 'NON'])->default('PAR');
            $table->enum('dia_nota', ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes'])->default('Lunes');
            $table->longText('notes')->nullable();
            $table->decimal('grand_total', 10, 2)->nullable();
            $table->json('notas_venta')->nullable();
            $table->date('fecha_liquidacion')->nullable();
            $table->foreignId('registrado_por')->constrained('users')->cascadeOnDelete();;
            $table->foreignId('solicitado_por')->constrained('users')->cascadeOnDelete();;
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
