<?php
namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class AdminUsuariosController extends Controller {
    public function pendientes(){
        return ['ok'=>true,'data'=> Usuario::where('estado_registro','Pendiente')->get()];
    }
    public function setEstado(Request $r, string $ci){
        $r->validate(['estado'=>'required|in:Pendiente,Aprobado,Rechazado']);
        $u = Usuario::findOrFail($ci);
        $u->estado_registro = $r->estado; 
        $u->save();
        return ['ok'=>true,'data'=>$u];
    }
}