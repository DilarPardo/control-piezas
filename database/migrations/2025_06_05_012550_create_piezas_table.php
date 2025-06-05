<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('piezas', function (Blueprint $table) {
            $table->string('id_pieza', 10)->primary();
            $table->string('pieza', 10);
            $table->decimal('peso_teorico', 8, 2);
            $table->decimal('peso_real', 8, 2)->nullable();
            $table->enum('estado', ['Fabricado', 'Pendiente']);
            $table->string('id_proyecto', 10);
            $table->string('id_bloque', 10);
            $table->date('fecha_registro');
            $table->string('registrado_por', 50)->nullable();
            $table->timestamps();

            $table->foreign('id_proyecto')->references('id_proyecto')->on('proyectos');
            $table->foreign('id_bloque')->references('id_bloque')->on('bloques');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('piezas');
    }
};