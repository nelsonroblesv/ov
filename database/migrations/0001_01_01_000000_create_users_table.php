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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->timestamp('birthday')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();

            // Fiscales
            $table->string('rfc')->nullable();
            $table->string('rfc_doc')->nullable();
            $table->string('curp')->nullable();
            $table->string('curp_doc')->nullable();
            $table->string('imss')->nullable();
            $table->string('imss_doc')->nullable();
            $table->string('comprobante_domicilio_doc')->nullable();
            $table->string('banco')->nullable();
            $table->string('cuenta')->nullable();
            $table->string('clabe')->nullable();

            // Empresa
            $table->string('email_empresa')->nullable();
            $table->string('phone_empresa')->nullable();
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_fin')->nullable();
            $table->string('licencia_image')->nullable();
            $table->string('ine_image')->nullable();
            $table->string('color')->nullable();
            $table->enum('role', ['Vendedor', 'Administrador', 'Gerente', 'Repartidor'])->default('Vendedor');
            $table->boolean('is_active')->default(true);

            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
