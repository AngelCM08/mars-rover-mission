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
        Schema::create('rover_positions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique(); // Para identificar al usuario por sesión
            $table->integer('x');
            $table->integer('y');
            $table->enum('direction', ['N', 'E', 'S', 'W']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rover_positions');
    }
};
