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
        Schema::create('sub_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('produced')->nullable();
            $table->integer('planed')->nullable();
            $table->integer('cost')->nullable();
            $table->unsignedBigInteger('statuss')->nullable()->default(0);
            $table->unsignedBigInteger('outsource_statuss')->nullable()->default(0);
            $table->foreign("plan_id")->references("id")->on("plans")->onDelete("cascade");
            $table->foreign("product_id")->references("id")->on("products")->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_plans');
    }
};
