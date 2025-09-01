<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'ci_usuario';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ci_usuario','primer_nombre','segundo_nombre','primer_apellido','segundo_apellido',
        'email','telefono','password','estado_registro','rol'
    ];

    protected $hidden = ['password'];
}