<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\SoftDeletes;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('delivery_address');
            $table->string('registration_nr')->nullable();
            $table->string('pvn_nr')->nullable();
            $table->string('bank')->nullable();
            $table->string('iban')->nullable();
            $table->integer('payment_term');
            $table->string('email')->nullable()->uniqiue();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
