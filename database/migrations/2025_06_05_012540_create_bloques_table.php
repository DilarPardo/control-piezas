<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bloques', function (Blueprint $table) {
            $table->string('id_bloque', 10)->primary();     // Ej: 130-1110, 135-1110...
            $table->string('nombre_bloque', 10);            // Ej: 1110, 2210, 3510...
            $table->string('id_proyecto', 10);              // FK a Tabla proyectos
            $table->foreign('id_proyecto')->references('id_proyecto')->on('proyectos');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('bloques');
    }
};