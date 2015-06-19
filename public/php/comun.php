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
                ->select('comentario.id')
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

    public function compruebaReportes(){
        $comp = ORM::for_table('reporte_comentario')
            ->where('eliminado',"0")
            ->count();

        return $comp;
    }

    public function compruebaRetos(){
        $pendientes = ORM::for_table('reto')
            ->raw_query('select eq1.nombre as nombreEq1,eq2.nombre as nombreEq2,eq1.logo as eq1Imagen,eq2.logo as eq2Imagen,reto.fecha,reto.mapa,reto.res_eq_retador as resEq1,reto.res_eq_retado as resEq2 from reto join equipo as eq1 on reto.retador_id=eq1.id join equipo as eq2 on reto.retado_id=eq2.id where reto.ganador IS null AND reto.aceptado IS NOT false ORDER BY reto.fecha ASC limit 10')
            ->find_many();
        $ar = count($pendientes);
        return $ar;
    }



}