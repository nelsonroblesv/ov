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
           $table->enum('status', ['entrega', 'cerrado', 'visita'])->default('visita');
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
