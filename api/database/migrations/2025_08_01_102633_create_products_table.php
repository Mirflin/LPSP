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
            $table->string('drawing_nr');
            $table->string('part_nr');
            $table->string('revision');
            $table->string('description')->nullable();
            $table->string('additional_info')->nullable();
            $table->unsignedBigInteger('client_id');
            $table->foreign("client_id")->references("id")->on("clients")->onDelete("cascade");
            $table->string('weight');
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
