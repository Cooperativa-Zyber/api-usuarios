<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('usuarios', function (Blueprint $t) {
            $t->string('ci_usuario', 20)->primary();
            $t->string('primer_nombre', 60);
            $t->string('segundo_nombre', 60)->nullable();
            $t->string('primer_apellido', 60);
            $t->string('segundo_apellido', 60)->nullable();
            $t->string('email')->nullable();
            $t->string('telefono', 30)->nullable();
            $t->string('password');
            $t->enum('estado_registro', ['Pendiente','Aprobado','Rechazado'])->default('Pendiente');
            $t->enum('rol', ['socio','admin'])->default('socio');
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('usuarios'); }
};