<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('administrativos', function (Blueprint $t) {
            $t->id();
            $t->string('ci_usuario', 20)->index();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('administrativos'); }
};