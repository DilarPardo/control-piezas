<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('registros', function (Blueprint $table) {
            $table->string('id_pieza', 10);                 // Igual que en piezas
            $table->string('pieza', 10);
            $table->decimal('peso_teorico', 8, 2);
            $table->decimal('peso_real', 8, 2)->nullable();
            $table->enum('estado', ['Fabricado', 'Pendiente']);
            $table->string('id_bloque', 10);
            $table->date('fecha_registro');
            $table->string('registrado_por', 50)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('registros');
    }
};