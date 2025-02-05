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
        Schema::create('prospectos', function (Blueprint $table) {
            $table->id();
            // Personal
            $table->string('name')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('notes')->nullable();

            $table->string('full_address')->nullable()->nullable();
            $table->decimal('latitude', 8,6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            $table->string('fachada')->nullable();

            $table->json('services')->nullable();

            //Sistema
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('tipo_prospecto', ['Posible', 'Prospecto'])->default('Posible');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospectos');
    }
};
