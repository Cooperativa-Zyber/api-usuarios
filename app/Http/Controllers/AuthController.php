<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Administrativo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $r)
    {
        $data = $r->validate([
            'ci_usuario'      => 'required|string|max:20|unique:usuarios,ci_usuario',
            'primer_nombre'   => 'required|string|max:60',
            'primer_apellido' => 'required|string|max:60',
            'segundo_nombre'  => 'nullable|string|max:60',
            'segundo_apellido'=> 'nullable|string|max:60',
            'email'           => 'nullable|email',
            'telefono'        => 'nullable|string|max:30',
            'password'        => 'required|string|min:6|max:100',
            'rol'             => 'nullable|in:socio,admin',
        ]);

        $u = Usuario::create([
            ...$data,
            'password'        => Hash::make($data['password']),
            'estado_registro' => 'Aprobado',
            'rol'             => $data['rol'] ?? 'socio',
        ]);

        if (($data['rol'] ?? 'socio') === 'admin') {
            Administrativo::firstOrCreate(['ci_usuario' => $data['ci_usuario']]);
        }

        return response()->json($u, 201);
    }

    public function login(Request $r)
    {
        $r->validate([
            'ci_usuario' => 'required|string|max:20',
            'password'   => 'required|string',
        ]);

        $u = Usuario::find($r->ci_usuario);
        if (!$u || !Hash::check($r->password, $u->password)) {
            throw ValidationException::withMessages(['ci_usuario' => ['Credenciales inválidas.']]);
        }
        if ($u->estado_registro !== 'Aprobado') {
            return response()->json(['message' => 'Usuario no aprobado'], 403);
        }

        $rol = Administrativo::where('ci_usuario', $u->ci_usuario)->exists() ? 'admin' : $u->rol;
        $token = $u->createToken('token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'rol'   => $rol,
            'user'  => [
                'ci_usuario' => $u->ci_usuario,
                'nombre'     => trim($u->primer_nombre.' '.$u->primer_apellido),
            ],
        ]);
    }

    public function me(Request $r)
    {
        $u = $r->user();
        if (!$u) return response()->json(['message' => 'No autenticado'], 401);

        $rol = Administrativo::where('ci_usuario', $u->ci_usuario)->exists() ? 'admin' : $u->rol;

        return response()->json([
            'user' => [
                'ci_usuario' => $u->ci_usuario,
                'nombre'     => trim($u->primer_nombre.' '.$u->primer_apellido),
                'estado'     => $u->estado_registro,
            ],
            'rol' => $rol,
        ]);
    }

    public function logout(Request $r)
    {
        if ($r->user()) $r->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'ok']);
    }
}