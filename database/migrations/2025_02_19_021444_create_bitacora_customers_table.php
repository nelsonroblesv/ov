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
        Schema::create('bitacora_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customers_id')->constrained()->cascadeOnDelete();
            $table->string('notas')->nullable();
            $table->boolean('show_video')->default(false);
            $table->json('testigo_1')->nullable();
            $table->json('testigo_2')->nullable();

           $table->enum('tipo_visita', ['entrega', 'cerrado', 'regular', 'prospectacion'])->default('regular');
           $table->string('foto_entrega')->nullable();
           $table->string('foto_stock_antes')->nullable();
           $table->string('foto_stock_despues')->nullable();
           $table->string('foto_lugar_cerrado')->nullable();
           $table->string('foto_stock_regular')->nullable();
           $table->string('foto_evidencia_prospectacion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bitacora_customers');
    }
};
