<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Administrativo;

class PerfilController extends Controller
{
    /**
     * Devuelve el perfil del usuario autenticado por Sanctum.
     * Incluye 'rol' (admin|socio) y campos normalizados.
     */
    public function perfil(Request $request)
    {
        $u = $request->user();

        // Detectamos rol por el tipo de modelo / tabla
        $rol = 'socio';
        if ($u instanceof Administrativo) {
            $rol = 'admin';
        } elseif ($u instanceof Usuario) {
            $rol = 'socio';
        } else {
            try {
                $table = method_exists($u, 'getTable') ? $u->getTable() : '';
                if ($table === 'administrativos') {
                    $rol = 'admin';
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        return response()->json([
            'ok'   => true,
            'data' => [
                'id'           => $u->id,
                'rol'          => $rol,
                'estado'       => $u->estado ?? 'Aprobado',
                'ci_usuario'   => $u->ci_usuario ?? ($u->ci ?? null),
                'email'        => $u->email ?? null,
                'nombre'       => $u->nombre ?? ($u->name ?? null),
            ],
        ]);
    }
}