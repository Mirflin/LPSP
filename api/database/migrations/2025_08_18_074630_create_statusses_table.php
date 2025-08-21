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
        Schema::create('statusses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        DB::table('statusses')->insert([
            ['name' => 'need aproval'],
            ['name' => 'production'],
            ['name' => 'invoice needed'],
            ['name' => 'completed'],
            ['name' => 'sended'],
            ['name' => 'not sended']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statusses');
    }
};
