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
        Schema::create('bitacora_prospeccions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospectos_id')->constrained()->cascadeOnDelete();
            $table->string('notas')->nullable();
            $table->string('testigo_1')->nullable();
            $table->string('testigo_2')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bitacora_prospeccions');
    }
};
