
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('proyectos', function (Blueprint $table) {
            $table->string('id_proyecto', 10)->primary(); // Ej: BICM, BALC, OPV, BRF
            $table->string('nombre', 100);               // Ej: Oceanográfico, Buque DA...
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('proyectos');
    }
};