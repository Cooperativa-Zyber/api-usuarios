<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Usuarios de prueba
        DB::table('usuarios')->updateOrInsert(
            ['ci' => '111111'],
            ['nombre'=>'Usuario Uno','email'=>'uno@example.com','password'=>Hash::make('secret'), 'created_at'=>now(), 'updated_at'=>now()]
        );
        DB::table('usuarios')->updateOrInsert(
            ['ci' => '222222'],
            ['nombre'=>'Usuario Dos','email'=>'dos@example.com','password'=>Hash::make('secret'), 'created_at'=>now(), 'updated_at'=>now()]
        );
        // Marcar 111111 como administrativo
        DB::table('administrativos')->updateOrInsert(
            ['ci_usuario' => '111111'],
            ['created_at'=>now(), 'updated_at'=>now()]
        );
    }
}
