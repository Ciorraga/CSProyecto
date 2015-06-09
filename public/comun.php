<?php

class comun {
    // Declaración de una propiedad
    public $var = null;

    // Declaración de un método
    public function mostrarSolicitudes($param) {
        $es_capitan = ORM::for_table('equipo')->where('capitan_id', $param)->find_one();
        if($es_capitan){
            $_SESSION['solicitudes'] = ORM::for_table('equipo_usuario')
                ->where('equipo_id', $es_capitan['id'])
                ->where('estado','pendiente')
                ->count();
            if($_SESSION['solicitudes']==0){
                $_SESSION['solicitudes'] = "vacio";
            }
        }
    }

    public function mostrarMensajes($param){
        $_SESSION['numMensajes'] = ORM::for_table('mensaje')
            ->where('usuario_id', $_SESSION['usuarioLogin']['id'])
            ->where('leido',0)->count();
    }
}