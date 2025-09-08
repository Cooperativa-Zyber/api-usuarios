<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $table = Schema::hasTable('solicitudes') ? 'solicitudes'
               : (Schema::hasTable('solicituds') ? 'solicituds' : null);

        if (!$table) return;

        // 1) Asegurar que exista 'ci'
        if (!Schema::hasColumn($table, 'ci')) {
            DB::statement("ALTER TABLE {$table} ADD COLUMN ci VARCHAR(20) NULL AFTER id");
        }

        // 2) Copiar datos desde 'ci_usuario' a 'ci' cuando haga falta
        if (Schema::hasColumn($table, 'ci_usuario')) {
            DB::statement("UPDATE {$table} SET ci = COALESCE(ci, ci_usuario)");
        }

        // 3) Si NO hay nulls, hacemos NOT NULL; sino lo dejamos NULL para no romper
        $hasNulls = DB::table($table)->whereNull('ci')->exists();
        if (!$hasNulls) {
            DB::statement("ALTER TABLE {$table} MODIFY ci VARCHAR(20) NOT NULL");
        }

        // (Opcional) Índice único
        try {
            DB::statement("ALTER TABLE {$table} ADD UNIQUE KEY {$table}_ci_unique (ci)");
        } catch (\Throwable $e) {
            // si ya existe, ignoramos
        }

        // 4) Eliminar 'ci_usuario'
        if (Schema::hasColumn($table, 'ci_usuario')) {
            DB::statement("ALTER TABLE {$table} DROP COLUMN ci_usuario");
        }
    }

    public function down(): void
    {
        $table = Schema::hasTable('solicitudes') ? 'solicitudes'
               : (Schema::hasTable('solicituds') ? 'solicituds' : null);

        if (!$table) return;

        // Volver a crear 'ci_usuario' y copiar desde 'ci' (solo para rollback)
        if (!Schema::hasColumn($table, 'ci_usuario')) {
            DB::statement("ALTER TABLE {$table} ADD COLUMN ci_usuario VARCHAR(20) NULL AFTER ci");
            DB::statement("UPDATE {$table} SET ci_usuario = ci WHERE ci_usuario IS NULL");
        }

        try { DB::statement("ALTER TABLE {$table} DROP INDEX {$table}_ci_unique"); } catch (\Throwable $e) {}
        // No eliminamos 'ci' en el rollback para evitar pérdida de datos.
    }
};