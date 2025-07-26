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
        Schema::create('cobros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained()->cascadeOnDelete();
            $table->foreignId('visita_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained(); // vendedor que lo capturó
            $table->foreignId('customer_id')->nullable()->constrained();
            $table->decimal('monto', 10, 2);
            $table->date('fecha_pago');
            $table->enum('tipo_pago', ['EF', 'TR', 'DP', 'CH', 'OT']);
            $table->text('comentarios')->nullable(); // ej: “cliente transfirió el domingo”
            $table->json('comprobantes')->nullable(); // para la imagen/foto
            $table->boolean('aprobado')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cobros');
    }
};
