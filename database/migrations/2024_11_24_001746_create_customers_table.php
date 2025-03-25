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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->string('name')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->date('birthday')->nullable();
            $table->string('avatar')->nullable();
            
            $table->string('full_address')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();

            $table->json('front_image')->nullable();
            $table->json('inside_image')->nullable();

            //$table->foreignId('regiones_id')->constrained()->cascadeOnDelete();
            //$table->foreignId('zonas_id')->constrained()->cascadeOnDelete();

             $table->string('regiones_id')->nullable();
            $table->string('zonas_id')->nullable();

            $table->json('services')->nullable();
            $table->boolean('reventa')->default(false);
            $table->boolean('is_preferred')->default(false);

            $table->string('extra')->nullable();

            $table->boolean('is_visible')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            //Facturacion
            $table->string('name_facturacion')->nullable();
            $table->string('razon_social')->nullable();
            $table->string('address_facturacion')->nullable();
            $table->string('postal_code_facturacion')->nullable();
            $table->enum('tipo_cfdi', ['Ninguno', 'Ingreso', 'Egreso', 'Traslado', 'Nomina'])
                    ->default('Ninguno')->nullable();
            $table->enum('tipo_razon_social', ['Ninguna', 'Sociedad Anonima', 'Sociedad Civil'])
                        ->default('Ninguna')->nullable();
            $table->string('cfdi_document')->nullable();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->enum('tipo_cliente', ['PV', 'RD', 'BK', 'SL', 'PR', 'PO'])->default('PV');
            $table->enum('simbolo', ['SB','BB', 'UN', 'OS', 'CR', 'UB', 'NC'])->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
