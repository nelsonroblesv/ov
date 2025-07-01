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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->enum('customer_type', ['N', 'R'])->default('R');
            $table->foreignId('zonas_id')->constrained('zonas')->cascadeOnDelete();
            $table->foreignId('regiones_id')->constrained('regiones')->cascadeOnDelete();
            $table->boolean('factura')->default(0);
            $table->string('num_pedido')->unique();
            $table->date('fecha_pedido')->nullable();
            $table->enum('tipo_nota', ['sistema', 'real', 'stock'])->default('sistema');
            $table->enum('tipo_semana_nota', ['P', 'N'])->default('P');
            $table->string('periodo')->unique();
            $table->enum('semana', ['1', '2', '3', '4'])->default('1');
            $table->enum('dia_nota', ['L', 'M', 'X', 'J', 'V'])->default('L');
            $table->string('num_ruta')->unique();
            $table->decimal('monto', 10, 2)->nullable();
            $table->string('estado_pedido')->unique();
            $table->date('fecha_entrega')->nullable();
            $table->date('fecha_liquidacion')->nullable();
            $table->foreignId('distribuidor')->constrained('users')->cascadeOnDelete();
            $table->foreignId('entrega')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reparto')->constrained('users')->cascadeOnDelete();
            $table->longText('observaciones')->nullable();
            $table->json('notas_venta')->nullable();
            $table->foreignId('registrado_por')->constrained('users')->cascadeOnDelete();;
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
