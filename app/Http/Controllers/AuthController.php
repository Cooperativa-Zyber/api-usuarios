   }

            $token = $admin->createToken('api')->plainTextToken;

            return response()->json([
                'ok'    => true,
                'token' => $token,
                'user'  => [
                    'id'         => $admin->id,
                    'rol'        => 'admin',
                    'estado'     => $admin->estado ?? 'Aprobado',
                    'ci_usuario' => $admin->ci ?? null,
                    'email'      => $admin->email ?? null,
                    'nombre'     => $admin->nombre ?? ($admin->name ?? null),
                ],
            ]);
        }

        // --------- 2) USUARIO / SOCIO ----------
        $socio = Usuario::query()
            ->where('ci_usuario', $login)
            ->orWhere('email', $login)
            ->orWhere('ci', $login) // por compatibilidad si existiera
            ->first();

        if (!$socio) {
            throw ValidationException::withMessages([
                'login' => 'Usuario no encontrado.',
            ]);
        }

        if (! $this->passwordMatch($pass, $socio->password)) {
            throw ValidationException::withMessages([
                'login' => 'Credenciales inválidas.',
            ]);
        }

        if (!$this->isApproved($socio->estado ?? null)) {
            return response()->json([
                'ok'     => false,
                'error'  => 'Usuario no aprobado aún.',
                'estado' => $socio->estado ?? 'Pendiente',
            ], 403);
        }

        $token = $socio->createToken('api')->plainTextToken;

        return response()->json([
            'ok'    => true,
            'token' => $token,
            'user'  => [
                'id'           => $socio->id,
                'rol'          => 'socio',
                'estado'       => $socio->estado ?? 'Aprobado',
                'ci_usuario'   => $socio->ci_usuario ?? ($socio->ci ?? null),
                'email'        => $socio->email ?? null,
                'nombre'       => $socio->nombre ?? ($socio->name ?? null),
            ],
        ]);
    }

    /**
     * (Opcional) Logout del token actual.
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json(['ok' => true]);
    }

    // ----------------- Helpers -----------------

    private function passwordMatch(string $plain, ?string $stored): bool
    {
        if (!$stored) {
            return false;
        }
        // Soporta hashed y texto plano (para ambientes de prueba)
        return Hash::check($plain, $stored) || hash_equals($stored, $plain);
    }

    private function isApproved(?string $estado): bool
    {
        if (!$estado) {
            // Si no hay campo estado, dejamos pasar (compatibilidad)
            return true;
        }
        $e = mb_strtolower($estado);
        return in_array($e, ['aprobado', 'aprobada', 'ok', 'activo', 'activa'], true);
    }
}