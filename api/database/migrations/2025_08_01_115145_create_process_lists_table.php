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
        Schema::create('process_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        DB::table('process_list')->insert([
            ['name' => 'Sheet Laser'],
            ['name' => 'Tube Laser'],
            ['name' => 'Outsourcing'],
            ['name' => 'Welding'],
            ['name' => 'Quality controll'],
            ['name' => 'Surface treatment'],
            ['name' => 'Kurz Powder paint'],
            ['name' => 'Phasing/Grinding'],
            ['name' => 'Folding'],
            ['name' => 'SPEC Packing'],
            ['name' => 'Threading/Drilling']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('process_lists');
    }
};
