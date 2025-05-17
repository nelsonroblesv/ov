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
        Schema::create('paquete_guias', function (Blueprint $table) {
            $table->id();
            $table->string('periodo');
            $table->string('semana');
            $table->string('num_semana');
            $table->foreignId('regiones_id');
            $table->enum('estado', ['rev', 'fal', 'com'])->default('rev');
            $table->foreignId('user_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paquete_guias');
    }
};
