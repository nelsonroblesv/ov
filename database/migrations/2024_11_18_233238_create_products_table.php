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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->foreignId('marca_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('familia_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->boolean('visibility')->default(true);
            $table->boolean('availability')->default(true);
            $table->decimal('price_distribuidor', 10, 2);
            $table->decimal('price_salon', 10, 2)->nullable();
            $table->decimal('price_publico', 10, 2);
           /* $table->string('sku')->nullable();*/
            $table->boolean('shipping')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
