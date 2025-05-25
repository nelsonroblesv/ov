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
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->enum('tipo', ['E', 'C'])->default('E');
            $table->string('notas')->nullable();
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
