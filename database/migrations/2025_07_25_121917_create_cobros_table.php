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
            $table->foreignId('pedidos_id')->constrained()->cascadeOnDelete();
            $table->foreignId('visitas_id')->constrained()->cascadeOnDelete();
            $table->foreignId('users_id')->nullable()->constrained(); // vendedor que lo capturó
            $table->decimal('monto', 10, 2);
            $table->date('fecha_pago');
            $table->enum('tipo_pago', ['efectivo', 'transferencia', 'otro']);
            $table->text('comentarios')->nullable(); // ej: “cliente transfirió el domingo”
            $table->string('comprobantes')->nullable(); // para la imagen/foto
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
