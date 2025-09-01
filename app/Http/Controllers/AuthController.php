<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // POST /api/registro
    public function register(Request $r)
    {
        $data = $r->validate([
            'ci_usuario'       => 'required|string|max:20|unique:usuarios,ci_usuario',
            'primer_nombre'    => 'required|string|max:60',
            'segundo_nombre'   => 'nullable|string|max:60',
            'primer_apellido'  => 'required|string|max:60',
            'segundo_apellido' => 'nullable|string|max:60',
            'email'            => 'required|email|max:120|unique:usuarios,email',
            'telefono'         => 'nullable|string|max:30',
            'password'         => 'required|string|min:6',
        ]);

        $u = Usuario::create([
            'ci_usuario'       => $data['ci_usuario'],
            'primer_nombre'    => $data['primer_nombre'],
            'segundo_nombre'   => $data['segundo_nombre'] ?? null,
            'primer_apellido'  => $data['primer_apellido'],
            'segundo_apellido' => $data['segundo_apellido'] ?? null,
            'email'            => $data['email'],
            'telefono'         => $data['telefono'] ?? null,
            'password'         => Hash::make($data['password']),
            'estado_registro'  => 'Pendiente',
            'rol'              => 'socio',
        ]);

        return response()->json([
            'ok'   => true,
            'user' => $u->only(['ci_usuario','primer_nombre','primer_apellido','email','estado_registro','rol']),
        ], 201);
    }

    // POST /api/login  (acepta CI o email en "login")
    public function login(Request $r)
    {
        $data = $r->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $u = filter_var($data['login'], FILTER_VALIDATE_EMAIL)
            ? Usuario::where('email', $data['login'])->first()
            : Usuario::where('ci_usuario', $data['login'])->first();

        if (!$u || !Hash::check($data['password'], $u->password)) {
            throw ValidationException::withMessages([
                'login' => ['Credenciales inválidas.'],
            ]);
        }

        $token = $u->createToken('token')->plainTextToken;

        return response()->json([
            'ok'    => true,
            'token' => $token,
            'user'  => $u->only(['ci_usuario','primer_nombre','primer_apellido','email','estado_registro','rol']),
        ]);
    }

    // GET /api/me  (protegido)
    public function me(Request $r)
    {
        return response()->json($r->user()->only([
            'ci_usuario','primer_nombre','primer_apellido','email','estado_registro','rol'
        ]));
    }

    // POST /api/logout  (protegido)
    public function logout(Request $r)
    {
        $r->user()->currentAccessToken()->delete();
        return response()->json(['ok' => true]);
    }
}