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
            // Direccion
            $table->foreignId('paises_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('estados_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('municipios_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('colonias_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('full_address')->nullable()->nullable();
            $table->decimal('latitude', 8,6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            //Sistema
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_possible')->default(true);
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
