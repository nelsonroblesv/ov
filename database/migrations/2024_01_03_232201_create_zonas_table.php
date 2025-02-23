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
        Schema::create('zonas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_zona')->nullable();
            $table->foreignId('regiones_id')->constrained();
            $table->enum('tipo_semana', ['PAR', 'NON'])->default('PAR');
            $table->enum('dia_zona', ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom'])->default('Dom');
            $table->foreignId('user_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zonas');
    }
};
