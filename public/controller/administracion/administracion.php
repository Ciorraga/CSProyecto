<?php

$app->get('/administracion', function() use ($app) {
    $req = new comun();
    $rep = $req->compruebaReportes();
    $ret = $req->compruebaRetos();
    if (!isset($_SESSION['usuarioLogin'])) {
        session_destroy();
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }else{
        $notic = $req->mostrarNoticias();

        $app->render('admin/listadoNoticias.html.twig', array('noticias' => $notic,'repNum' => $rep, 'retNum' => $ret));
    }
});

$app->get('/administracion/listanoticias', function() use ($app) {

    if (!isset($_SESSION['usuarioLogin'])) {
        session_destroy();
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }else{
        $req = new comun();
        $notic = $req->mostrarNoticias();
        $rep = $req->compruebaReportes();
        $ret = $req->compruebaRetos();

        $app->render('admin/listadoNoticias.html.twig', array('noticias' => $notic,'repNum' => $rep, 'retNum' => $ret));
        die();
    }
})->name("listaNoticias");

$app->get('/administracion/nuevanoticia', function() use ($app) {
    if (!isset($_SESSION['usuarioLogin'])) {
        session_destroy();
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }else{
        $req = new comun();
        $notic = $req->mostrarNoticias();
        $rep = $req->compruebaReportes();
        $ret = $req->compruebaRetos();
        $app->render('admin/nuevanoticia.html.twig',array('repNum' => $rep, 'retNum' => $ret));
        die();
    }
})->name("nuevaNoticia");

$app->get('/administracion/usuarios', function() use ($app) {
    if (!isset($_SESSION['usuarioLogin'])) {
        session_destroy();
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }else{
        $usuarios = ORM::for_table('usuario')
            ->select('usuario.id')
            ->select('usuario.imagen')
            ->select('usuario.user')
            ->select('usuario.nombre')
            ->select('usuario.email')
            ->select('usuario.edad')
            ->find_many();

        $req = new comun();
        $notic = $req->mostrarNoticias();
        $rep = $req->compruebaReportes();
        $ret = $req->compruebaRetos();

        $app->render('admin/listaUsuarios.html.twig',array('usuarios' => $usuarios,'repNum' => $rep, 'retNum' => $ret));
        die();
    }
})->name("listaUsuarios");

$app->get('/administracion/equipos', function() use ($app) {
    if (!isset($_SESSION['usuarioLogin'])) {
        session_destroy();
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }else{
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

        $app->render('admin/listaEquipos.html.twig',array('equipos' => $equipos,'repNum' => $rep, 'retNum' => $ret));
        die();
    }
})->name("listaEquipos");

$app->get('/administracion/retos', function() use ($app) {
    if (!isset($_SESSION['usuarioLogin'])) {
        session_destroy();
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }else{
        $retosEq = ORM::for_table('reto')
            ->raw_query('select eq1.nombre as nombreEq1,eq2.nombre as nombreEq2,eq1.logo as eq1Imagen,eq2.logo as eq2Imagen,reto.id,reto.fecha,reto.mapa,reto.res_eq_retador as resEq1,reto.res_eq_retado as resEq2 from reto join equipo as eq1 on reto.retador_id=eq1.id join equipo as eq2 on reto.retado_id=eq2.id where reto.ganador IS null AND reto.aceptado IS NOT false ORDER BY reto.fecha ASC limit 10')
            ->find_many();

        $retosCerrados = ORM::for_table('reto')
            ->raw_query('select eq1.nombre as nombreEq1,eq2.nombre as nombreEq2,eq1.logo as eq1Imagen,eq2.logo as eq2Imagen,reto.id,reto.fecha,reto.mapa,reto.res_eq_retador as resEq1,reto.res_eq_retado as resEq2 from reto join equipo as eq1 on reto.retador_id=eq1.id join equipo as eq2 on reto.retado_id=eq2.id where reto.ganador IS NOT null ORDER BY reto.fecha DESC ')
            ->find_many();

        $req = new comun();
        $notic = $req->mostrarNoticias();
        $rep = $req->compruebaReportes();
        $ret = $req->compruebaRetos();

        $app->render('admin/listaRetos.html.twig',array('retosEq' => $retosEq,'retosCerrados' => $retosCerrados,'repNum' => $rep, 'retNum' => $ret));
        die();
    }
})->name("listaRetos");

$app->get('/administracion/retos1vs1', function() use ($app) {
    if (!isset($_SESSION['usuarioLogin'])) {
        session_destroy();
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }else{
        $retosAbiertos = ORM::for_table('reto1vs1')
            ->raw_query("select us1.user as user1,us2.user as user2,us1.imagen as us1Imagen,us2.imagen as us2Imagen,reto1vs1.fecha,reto1vs1.mapa,reto1vs1.id
        from reto1vs1 join usuario as us1 on reto1vs1.retador_id=us1.id join usuario as us2 on reto1vs1.retado_id=us2.id where reto1vs1.ganador IS null  ORDER BY reto1vs1.fecha ASC")
            ->find_many();

        $retosCerrados = ORM::for_table('reto')
            ->raw_query("select us1.user as user1,us2.user as user2,us1.imagen as us1Imagen,us2.imagen as us2Imagen,reto1vs1.fecha,reto1vs1.mapa,reto1vs1.res_retador as resUs1,reto1vs1.res_retado as resUs2,reto1vs1.id
        from reto1vs1 join usuario as us1 on reto1vs1.retador_id=us1.id join usuario as us2 on reto1vs1.retado_id=us2.id where reto1vs1.ganador IS NOT null ORDER BY reto1vs1.fecha ASC")
            ->find_many();

        $req = new comun();
        $notic = $req->mostrarNoticias();
        $rep = $req->compruebaReportes();
        $ret = $req->compruebaRetos();

        $app->render('admin/listaRetos1vs1.html.twig',array('retosEq' => $retosAbiertos,
            'retosCerrados' => $retosCerrados,
            'repNum' => $rep,
            'retNum' => $ret));
        die();
    }
})->name("listaRetos1vs1");

$app->get('/retoActualiza1vs1/:id', function ($id) {
    $equipos = ORM::for_table('reto')
        ->where('id',$id)
        ->find_one();

    echo "<form action='/' method='POST'>";
    echo "<span style='margin-left:25px;'>Retador: <input type='text' name='resRetador'/></span>" ;
    echo "<span style='margin-left:5px;'>Retado: <input type='text' name='resRetado'/></span>" ;
    echo "<button style='float:right;' type='submit' class='btn btn-success' name='fijaResultadoReto1vs1' value='".$id."'>Actualizar</button>";
    echo "</form>";

})->name('actualizarRetos1vs1');

$app->get('/retoActualiza/:id', function ($id) {
        $equipos = ORM::for_table('reto')
            ->where('id',$id)
            ->find_one();

        echo "<form action='/' method='POST'>";
            echo "<span style='margin-left:25px;'>Retador: <input type='text' name='resRetador'/></span>" ;
            echo "<span style='margin-left:5px;'>Retado: <input type='text' name='resRetado'/></span>" ;
            echo "<button style='float:right;' type='submit' class='btn btn-success' name='fijaResultadoReto' value='".$id."'>Actualizar</button>";
        echo "</form>";

})->name('actualizarRetos');

$app->get('/administracion/reportes', function() use ($app) {
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
    $app->render('admin/reportes.html.twig',array('repNum' => $rep, 'retNum' => $ret,'reportes' => $reportes));
    die();
});

$app->get('/muestraReporte/:id', function ($id) {
    //$id es is del reporte
    $comentarioReporte = ORM::for_table('comentario')
        ->join('usuario',array('comentario.usuario_id','=','usuario.id'))
        ->where('comentario.id',$id)
        ->select('comentario.id')
        ->select('comentario.texto')
        ->select('usuario.user')
        ->find_one();
    $idReporte = ORM::for_table('reporte_comentario')
        ->where('comentario_id',$id)
        ->where('comprobado','0')
        ->find_one();


    echo "<form action='/' method='POST'>
              <td>Autor:".$comentarioReporte['user']."</td>
              <td>".$comentarioReporte['texto']."</td>
              <td>
                <button type='submit' class='btn btn-danger'  value='". $comentarioReporte['id'] ."' name='eliminaComentarioReportado'>Eliminar</button><br/><br/>
                <button type='submit' class='btn btn-warning'  value='". $idReporte['id'] ."' name='descartaComentarioReportado'>Descartar</button>
              </td>
          </form>";

})->name('muestraRep');



