<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Models\Administrativo;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $ci = '99999999';
        $u = Usuario::updateOrCreate(
            ['ci_usuario' => $ci],
            [
                'primer_nombre'   => 'Admin',
                'primer_apellido' => 'Coop',
                'password'        => Hash::make('admin123'),
                'estado_registro' => 'Aprobado',
                'rol'             => 'admin',
            ]
        );
        Administrativo::firstOrCreate(['ci_usuario' => $ci]);
    }
}