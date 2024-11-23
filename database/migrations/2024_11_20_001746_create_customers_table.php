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
            $table->string('alias')->unique();
            $table->string('name')->unique();
            $table->string('email');
            $table->string('phone');
            $table->string('avatar');
            $table->string('address');
            $table->string('state_id');
            $table->string('municipality_id');
            $table->string('locality');
            $table->string('zip_code');
            $table->string('contact');
            $table->string('front_image');
            $table->string('inside_image');
            $table->string('coordinate');
            $table->enum('type' , ['par', 'non']);
            $table->string('extra');
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
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
