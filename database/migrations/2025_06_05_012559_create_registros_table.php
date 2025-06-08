<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('registros', function (Blueprint $table) {
            $table->id('id_registro'); // Clave primaria autoincremental
            $table->string('id_pieza', 10);
            $table->string('pieza', 10);
            $table->decimal('peso_teorico', 8, 2);
            $table->decimal('peso_real', 8, 2)->nullable();
            $table->enum('estado', ['Fabricado', 'Pendiente']);
            $table->string('id_bloque', 10);
            $table->date('fecha_registro');
            $table->string('registrado_por', 50)->nullable();

            $table->timestamps();

            // Foreign keys (si existen en tablas relacionadas)
            $table->foreign('id_bloque')->references('id_bloque')->on('bloques')->onDelete('cascade');
            // También podrías tener:
            $table->foreign('id_pieza')->references('id_pieza')->on('piezas');
        });
    }

    public function down(): void {
        Schema::dropIfExists('registros');
    }
};