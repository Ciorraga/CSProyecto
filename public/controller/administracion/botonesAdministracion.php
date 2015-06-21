<?php

if(isset($_POST['botonBorrarNoticia'])){
    $req = new comun();
    $notic = $req->mostrarNoticias();
    ORM::for_table('noticia')
        ->find_one($_POST['botonBorrarNoticia'])->delete();

    $app->redirect($app->router()->urlFor('listaNoticias'));
    die();
}


if(isset($_POST['botonEnviaNoticia'])){
    if($_POST['tituloNoticia']=="" || $_POST['textoNoticia']==""){
        $req = new comun();
        $ret = $req->compruebaRetos();
        $app->render('admin/nuevaNoticia.html.twig',array('mensajeError' => 'Tienes que rellenar todos los campos','retNum' => $ret));
    }else{
        $fecha=date("Y/m/d");
        $nuevaNoticia = ORM::for_table('noticia')
            ->create();
        $nuevaNoticia->titulo = $_POST['tituloNoticia'];
        $nuevaNoticia->texto = $_POST['textoNoticia'];
        $nuevaNoticia->fecha = $fecha;
        $nuevaNoticia->usuario_id = $_SESSION['usuarioLogin']['id'];
        $nuevaNoticia->save();

        $req = new comun();
        $notic = $req->mostrarNoticias();
        $ret = $req->compruebaRetos();

        $app->render('admin/listadoNoticias.html.twig',array('noticias' => $notic,'mensajeOk' => 'Noticia agregada con éxito','retNum' => $ret));
    }
}

if(isset($_POST['botonAdminBorrarUsuario'])){
    $consEqId = ORM::for_table('usuario')
        ->where('id',$_POST['botonAdminBorrarUsuario'])
        ->find_one();

    $equipoCapitan = ORM::for_table('equipo')
        ->where('capitan_id',$_POST['botonAdminBorrarUsuario'])
        ->find_one();

    if($equipoCapitan) {
        $consIntegrantes = ORM::for_table('usuario')
            ->where('equipo_id', $consEqId['equipo_id'])
            ->find_many();

        if (count($consIntegrantes) == 1) {
            $equipoCapitan->nombre="Eq. eliminado";
            $equipoCapitan->capitan_id=null;
            $equipoCapitan->save();
        } else {
            $equipoCambiaCap = ORM::for_table('usuario')
                ->where('equipo_id', $equipoCapitan['id'])
                ->where_not_equal('id', $_POST['botonAdminBorrarUsuario'])
                ->find_many();

            $equipoCapitan->capitan_id = $consIntegrantes[1]['id'];
            $equipoCapitan->save();
        }
    }
    $borrar = ORM::for_table('usuario')
        ->find_one($_POST['botonAdminBorrarUsuario']);

    $borrar->delete();

    $usuarios = ORM::for_table('usuario')
        ->select('usuario.id')
        ->select('usuario.imagen')
        ->select('usuario.user')
        ->select('usuario.nombre')
        ->select('usuario.email')
        ->select('usuario.edad')
        ->find_many();

    $req = new comun();
    $ret = $req->compruebaRetos();

    $app->render('admin/listaUsuarios.html.twig', array('mensajeOk' => 'Usuario eliminado con éxito','usuarios' => $usuarios,'retNum' => $ret));
}

if(isset($_POST['botonAdminBorrarEquipo'])){
    $miembros = ORM::for_table('usuario')
        ->where('equipo_id',$_POST['botonAdminBorrarEquipo'])
        ->find_many();
    foreach ($miembros as $miembro){
        $miembro->equipo_id = null;
        $miembro->save();
    }

    $equipo = ORM::for_table('equipo')
        ->where('id',$_POST['botonAdminBorrarEquipo'])
        ->find_one();
    $equipo->capitan_id = null;
    $equipo->save();
    $equipo->delete();

    $equipos = ORM::for_table('equipo')
        ->join('usuario',array('equipo.capitan_id','=','usuario.id'))
        ->select('equipo.id')
        ->select('equipo.logo')
        ->select('equipo.nombre')
        ->select('usuario.user')
        ->find_many();

    $req = new comun();
    $notic = $req->mostrarNoticias();
    $rep = $req->compruebaReportes();
    $ret = $req->compruebaRetos();

    $app->render('admin/listaEquipos.html.twig',array('mensajeOK' => 'Equipo eliminado con éxito','equipos' => $equipos,'repNum' => $rep, 'retNum' => $ret));
    die();
}

if(isset($_POST['fijaResultadoReto'])){
    $reto = ORM::for_table('reto')
        ->where('id',$_POST['fijaResultadoReto'])
        ->find_one();
    //var_dump($reto);die();

    $reto->res_eq_retador = $_POST['resRetador'];
    $reto->res_eq_retado = $_POST['resRetado'];
    if($_POST['resRetador']>$_POST['resRetado']){
        $reto->ganador = $reto['retador_id'];
    }else{
        $reto->ganador = $reto['retado_id'];
    }
    $reto->save();

    $retosEq = ORM::for_table('reto')
        ->raw_query('select eq1.nombre as nombreEq1,eq2.nombre as nombreEq2,eq1.logo as eq1Imagen,eq2.logo as eq2Imagen,reto.id,reto.fecha,reto.mapa,reto.res_eq_retador as resEq1,reto.res_eq_retado as resEq2 from reto join equipo as eq1 on reto.retador_id=eq1.id join equipo as eq2 on reto.retado_id=eq2.id where reto.ganador IS null AND reto.aceptado IS NOT false ORDER BY reto.fecha ASC limit 10')
        ->find_many();

    $retosCerrados = ORM::for_table('reto')
        ->raw_query('select eq1.nombre as nombreEq1,eq2.nombre as nombreEq2,eq1.logo as eq1Imagen,eq2.logo as eq2Imagen,reto.id,reto.fecha,reto.mapa,reto.res_eq_retador as resEq1,reto.res_eq_retado as resEq2 from reto join equipo as eq1 on reto.retador_id=eq1.id join equipo as eq2 on reto.retado_id=eq2.id where reto.ganador IS NOT null ORDER BY reto.fecha DESC ')
        ->find_many();

    $req = new comun();
    $ret = $req->compruebaRetos();

    $app->render('admin/listaRetos.html.twig',array('retosEq' => $retosEq, 'mensajeOk' => 'Reto actualizado','retosCerrados' => $retosCerrados,'retNum' => $ret));
    die();
}

if(isset($_POST['fijaResultadoReto1vs1'])){
    $reto = ORM::for_table('reto1vs1')
        ->where('id',$_POST['fijaResultadoReto1vs1'])
        ->find_one();

    $reto->res_retador = $_POST['resRetador'];
    $reto->res_retado = $_POST['resRetado'];
    if($_POST['resRetador']>$_POST['resRetado']){
        $reto->ganador = $reto['retador_id'];
    }else{
        $reto->ganador = $reto['retado_id'];
    }
    $reto->save();

    $retosAbiertos = ORM::for_table('reto1vs1')
        ->raw_query("select us1.user as user1,us2.user as user2,us1.imagen as us1Imagen,us2.imagen as us2Imagen,reto1vs1.fecha,reto1vs1.mapa,reto1vs1.id
        from reto1vs1 join usuario as us1 on reto1vs1.retador_id=us1.id join usuario as us2 on reto1vs1.retado_id=us2.id where reto1vs1.ganador IS null  ORDER BY reto1vs1.fecha ASC")
        ->find_many();

    $retosCerrados = ORM::for_table('reto')
        ->raw_query("select us1.user as user1,us2.user as user2,us1.imagen as us1Imagen,us2.imagen as us2Imagen,reto1vs1.fecha,reto1vs1.mapa,reto1vs1.res_retador as resUs1,reto1vs1.res_retado as resUs2,reto1vs1.id
        from reto1vs1 join usuario as us1 on reto1vs1.retador_id=us1.id join usuario as us2 on reto1vs1.retado_id=us2.id where reto1vs1.ganador IS NOT null ORDER BY reto1vs1.fecha ASC")
        ->find_many();

    $req = new comun();
    $ret = $req->compruebaRetos();

    $app->render('admin/listaRetos1vs1.html.twig',array('retosEq' => $retosAbiertos,
        'retNum' => $ret,
        'mensajeOk' => 'Reto actualizado',
        'retosCerrados' => $retosCerrados));
    die();
}

if(isset($_POST['botonBorrarReto'])){
    $reto = ORM::for_table('reto')
        ->where('id',$_POST['botonBorrarReto'])
        ->find_one();
    $reto->delete();

    $retosEq = ORM::for_table('reto')
        ->raw_query('select eq1.nombre as nombreEq1,eq2.nombre as nombreEq2,eq1.logo as eq1Imagen,eq2.logo as eq2Imagen,reto.id,reto.fecha,reto.mapa,reto.res_eq_retador as resEq1,reto.res_eq_retado as resEq2 from reto join equipo as eq1 on reto.retador_id=eq1.id join equipo as eq2 on reto.retado_id=eq2.id where reto.ganador IS null AND reto.aceptado IS NOT false ORDER BY reto.fecha ASC limit 10')
        ->find_many();
    $retosCerrados = ORM::for_table('reto')
        ->raw_query('select eq1.nombre as nombreEq1,eq2.nombre as nombreEq2,eq1.logo as eq1Imagen,eq2.logo as eq2Imagen,reto.id,reto.fecha,reto.mapa,reto.res_eq_retador as resEq1,reto.res_eq_retado as resEq2 from reto join equipo as eq1 on reto.retador_id=eq1.id join equipo as eq2 on reto.retado_id=eq2.id where reto.ganador IS NOT null ORDER BY reto.fecha DESC ')
        ->find_many();

    $req = new comun();
    $ret = $req->compruebaRetos();

    $app->render('admin/listaRetos.html.twig',array('retosEq' => $retosEq, 'mensajeOk' => 'Reto borrado','retosCerrados' => $retosCerrados,'retNum' => $ret));
    die();
}

if(isset($_POST['botonBorrarReto1vs1'])){
    $reto = ORM::for_table('reto1vs1')
        ->where('id',$_POST['botonBorrarReto1vs1'])
        ->find_one();
    $reto->delete();

    $retosAbiertos = ORM::for_table('reto1vs1')
        ->raw_query("select us1.user as user1,us2.user as user2,us1.imagen as us1Imagen,us2.imagen as us2Imagen,reto1vs1.fecha,reto1vs1.mapa,reto1vs1.id
        from reto1vs1 join usuario as us1 on reto1vs1.retador_id=us1.id join usuario as us2 on reto1vs1.retado_id=us2.id where reto1vs1.ganador IS null  ORDER BY reto1vs1.fecha ASC")
        ->find_many();

    $retosCerrados = ORM::for_table('reto')
        ->raw_query("select us1.user as user1,us2.user as user2,us1.imagen as us1Imagen,us2.imagen as us2Imagen,reto1vs1.fecha,reto1vs1.mapa,reto1vs1.res_retador as resUs1,reto1vs1.res_retado as resUs2,reto1vs1.id
        from reto1vs1 join usuario as us1 on reto1vs1.retador_id=us1.id join usuario as us2 on reto1vs1.retado_id=us2.id where reto1vs1.ganador IS NOT null ORDER BY reto1vs1.fecha ASC")
        ->find_many();

    $req = new comun();
    $ret = $req->compruebaRetos();

    $app->render('admin/listaRetos1vs1.html.twig',array('retosEq' => $retosAbiertos,
        'retNum' => $ret,
        'mensajeOk' => 'Reto borrado',
        'retosCerrados' => $retosCerrados));
    die();
}

if(isset($_POST['botonBorrarReporte'])){
    $reporte = ORM::for_table('reporte_comentario')
        ->where('id',$_POST['botonBorrarReporte'])
        ->find_one();
    $reporte->delete();

    $reportes = ORM::for_table('reporte_comentario')
        ->join('usuario', array('reporte_comentario.usuario_id', '=', 'usuario.id'))
        ->join('comentario',array('reporte_comentario.usuario_id','=','comentario.id'))
        ->select('reporte_comentario.id')
        ->select('usuario.user')
        ->select('comentario.texto')
        ->select('comentario.id')
        ->select('reporte_comentario.fecha')
        ->order_by_asc('reporte_comentario.fecha')
        ->find_array();

    $req = new comun();
    $notic = $req->mostrarNoticias();
    $rep = $req->compruebaReportes();
    $ret = $req->compruebaRetos();
    $app->render('admin/reportes.html.twig',array('repNum' => $rep, 'retNum' => $ret,'reportes' => $reportes,'mensajeOk' => 'Reporte eliminado con éxito'));
    die();
}

if(isset($_POST['eliminaComentarioReportado'])){
    $eliminaComentario = ORM::for_table('comentario')
        ->find_one($_POST['eliminaComentarioReportado']);
    $eliminaComentario->delete();

    $reportes = ORM::for_table('reporte_comentario')
        ->join('usuario', array('reporte_comentario.usuario_id', '=', 'usuario.id'))
        ->select('reporte_comentario.id')
        ->select('usuario.user')
        ->select('reporte_comentario.fecha')
        ->where('reporte_comentario.comprobado','0')
        ->order_by_asc('reporte_comentario.fecha')
        ->find_array();

    $req = new comun();
    $notic = $req->mostrarNoticias();
    $rep = $req->compruebaReportes();
    $ret = $req->compruebaRetos();
    $app->render('admin/reportes.html.twig',array('repNum' => $rep, 'retNum' => $ret,'reportes' => $reportes, 'mensajeOk' => 'Comentario eliminado con éxito'));
    die();
}

if(isset($_POST['descartaComentarioReportado'])){
    $descartaComentario = ORM::for_table('reporte_comentario')
        ->where('id',$_POST['descartaComentarioReportado'])
        ->find_one();
    $descartaComentario->comprobado = "1";
    $descartaComentario->save();

    $reportes = ORM::for_table('reporte_comentario')
        ->join('usuario', array('reporte_comentario.usuario_id', '=', 'usuario.id'))
        ->select('reporte_comentario.id')
        ->select('reporte_comentario.comentario_id')
        ->select('usuario.user')
        ->select('reporte_comentario.fecha')
        ->where('reporte_comentario.comprobado','0')
        ->order_by_asc('reporte_comentario.fecha')
        ->find_array();

    $req = new comun();
    $notic = $req->mostrarNoticias();
    $rep = $req->compruebaReportes();
    $ret = $req->compruebaRetos();
    $app->render('admin/reportes.html.twig',array('repNum' => $rep, 'retNum' => $ret,'reportes' => $reportes, 'mensajeOk' => 'Reporte de comentario descartado con éxito'));
    die();
}



