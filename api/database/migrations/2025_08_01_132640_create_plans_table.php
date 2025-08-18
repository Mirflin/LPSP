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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('po_date');
            $table->string('po_nr');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('produced')->nullable();
            $table->integer('sended')->nullable();
            $table->float('price')->nullable();
            $table->float('total')->nullable();
            $table->integer('statuss')->nullable()->default(0);
            $table->string('extra_process')->nullable();
            $table->string('invoice')->nullable();
            $table->unsignedBigInteger('outsource_statuss')->nullable()->default(0);
            $table->foreign("client_id")->references("id")->on("clients")->onDelete("cascade");
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
            $table->foreign("product_id")->references("id")->on("products")->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
