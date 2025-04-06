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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade'); // Remitente
            $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');   // Destinatario
            $table->string('asunto')->nullable();
            $table->string('mensaje')->nullable();
            $table->boolean('estado')->default(0);
            $table->longText('adjuntos')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
