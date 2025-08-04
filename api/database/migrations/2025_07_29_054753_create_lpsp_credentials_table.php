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
        Schema::create('lpsp_credentials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('bank');
            $table->string('export_address');
            $table->string('phone');
            $table->string('registration_nr');
            $table->string('vat_nr');
            $table->string('iban');
            $table->timestamps();
        });

        DB::table('lpsp_credentials')->insert([
            ['name' => 'LPSP', 'address' => '...', 'bank' => '...', 'export_address' => '...', 'phone' => '...', 'registration_nr' => '...', 'vat_nr' => '...', 'iban' => '...']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lpsp_credentials');
    }
};
