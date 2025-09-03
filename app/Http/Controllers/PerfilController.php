<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PerfilController extends Controller {
    public function show(Request $r){
        return ['ok'=>true,'data'=>$r->user()];
    }
    public function update(Request $r){
        $u = $r->user();
        $data = $r->validate([
            'primer_nombre'   => 'sometimes|string|max:60',
            'segundo_nombre'  => 'sometimes|nullable|string|max:60',
            'primer_apellido' => 'sometimes|string|max:60',
            'segundo_apellido'=> 'sometimes|nullable|string|max:60',
        ]);
        $u->update($data);
        return ['ok'=>true,'data'=>$u];
    }
}