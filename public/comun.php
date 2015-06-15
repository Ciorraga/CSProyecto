<?php

class comun {
    public $var = null;

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
        }else{
            $_SESSION['solicitudes'] = "noCapi";
        }
    }

    public function mostrarMensajes(){
        $_SESSION['numMensajes'] = ORM::for_table('mensaje')
            ->where('usuario_id', $_SESSION['usuarioLogin']['id'])
            ->where('leido',0)->count();
    }

    public function mostrarNoticias(){
        $noticias = ORM::for_table('noticia')
            ->select('noticia.id')
            ->select('noticia.titulo')
            ->select('noticia.texto')
            ->select('noticia.fecha')
            ->select('usuario.user')
            ->join('usuario', array('noticia.usuario_id', '=', 'usuario.id'))
            ->order_by_desc('noticia.fecha')
            ->find_array();

        $miArray = [];
        $i = 0;
        foreach($noticias as $item){
            $comentarios = ORM::for_table('comentario')
                ->select('comentario.texto')
                ->select('comentario.fecha')
                ->select('usuario.imagen')
                ->select('usuario.user')
                ->join('usuario', array('comentario.usuario_id', '=', 'usuario.id'))
                ->where('noticia_id',$item['id'])
                ->order_by_desc('comentario.fecha')
                ->find_many();

            $miArray[$i]['id'] = $item['id'];
            $miArray[$i]['titulo'] = $item['titulo'];
            $miArray[$i]['texto'] = $item['texto'];
            $miArray[$i]['fecha'] = $item['fecha'];
            $miArray[$i]['user'] = $item['user'];
            $miArray[$i]['comentarios'] = $comentarios;
            $i++;
        }
        return $miArray;
    }

    public function compruebaNombres($tabla,$donde,$que){
        $comp = ORM::for_table($tabla)
            ->where($donde,$que)
            ->find_one();
        return $comp;
    }


}