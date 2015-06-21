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
            ->where('comprobado',"0")
            ->count();

        return $comp;
    }

    public function compruebaRetos(){
        $res = [];
        $pendientes = ORM::for_table('reto')
            ->raw_query('select eq1.nombre as nombreEq1,eq2.nombre as nombreEq2,eq1.logo as eq1Imagen,eq2.logo as eq2Imagen,reto.fecha,reto.mapa,reto.res_eq_retador as resEq1,reto.res_eq_retado as resEq2 from reto join equipo as eq1 on reto.retador_id=eq1.id join equipo as eq2 on reto.retado_id=eq2.id where reto.ganador IS null AND reto.aceptado IS NOT false ORDER BY reto.fecha ASC limit 10')
            ->find_many();

        $pendientes1vs1 = ORM::for_table('reto1vs1')
            ->raw_query('select us1.user as user1,us2.user as user2,us1.imagen as us1Imagen,us2.imagen as us2Imagen,reto1vs1.fecha,reto1vs1.mapa,reto1vs1.id
        from reto1vs1 join usuario as us1 on reto1vs1.retador_id=us1.id join usuario as us2 on reto1vs1.retado_id=us2.id where reto1vs1.ganador IS null  ORDER BY reto1vs1.fecha ASC')
            ->find_many();

        $us = count($pendientes1vs1);
        $eq = count($pendientes);
        $total=$us+$eq;

        $res[0] = $us;
        $res[1] = $eq;
        $res[2] = $total;

        return $res;
    }

    public function compruebaRetosUsuario(){
        $res = ORM::for_table('reto1vs1')
            ->where('retado_id',$_SESSION['usuarioLogin']['id'])
            ->where('aceptado',"0")
            ->find_many();

        return $res;
    }

    public function compruebaRetosEquipo(){
        $es_capitan = ORM::for_table('equipo')
            ->where('capitan_id',$_SESSION['usuarioLogin']['id'])
            ->find_one();

        if($es_capitan){
            $res = ORM::for_table('reto')
                ->where('retado_id',$_SESSION['usuarioLogin']['equipo_id'])
                ->where('aceptado',"0")
                ->find_many();
        }else{
            $res = null;
        }

        return $res;
    }




}