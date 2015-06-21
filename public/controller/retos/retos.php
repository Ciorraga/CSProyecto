<?php

$app->get('/retos', function() use ($app) {
    if (!isset($_SESSION['usuarioLogin'])) {
        $ultJugados = ORM::for_table('reto')
            ->raw_query('select eq1.nombre as nombreEq1,eq2.nombre as nombreEq2,eq1.logo as eq1Imagen,eq2.logo as eq2Imagen,reto.fecha,reto.mapa,reto.res_eq_retador as resEq1,reto.res_eq_retado as resEq2 from reto join equipo as eq1 on reto.retador_id=eq1.id join equipo as eq2 on reto.retado_id=eq2.id where reto.ganador IS NOT null ORDER BY reto.fecha DESC limit 10')
            ->find_many();

        $pendientes = ORM::for_table('reto')
            ->raw_query('select eq1.nombre as nombreEq1,eq2.nombre as nombreEq2,eq1.logo as eq1Imagen,eq2.logo as eq2Imagen,reto.fecha,reto.mapa,reto.res_eq_retador as resEq1,reto.res_eq_retado as resEq2 from reto join equipo as eq1 on reto.retador_id=eq1.id join equipo as eq2 on reto.retado_id=eq2.id where reto.ganador IS null AND reto.aceptado = 1 ORDER BY reto.fecha ASC limit 10')
            ->find_many();

        $app->render('retos.html.twig',array(
            'ultimosJugados' => $ultJugados,
            'pendientes' => $pendientes));
    }else{
        $ultJugados = ORM::for_table('reto')
            ->raw_query('select eq1.nombre as nombreEq1,eq2.nombre as nombreEq2,eq1.logo as eq1Imagen,eq2.logo as eq2Imagen,reto.fecha,reto.mapa,reto.res_eq_retador as resEq1,reto.res_eq_retado as resEq2 from reto join equipo as eq1 on reto.retador_id=eq1.id join equipo as eq2 on reto.retado_id=eq2.id where reto.ganador IS NOT null ORDER BY reto.fecha DESC limit 10')
            ->find_many();

        $pendientes = ORM::for_table('reto')
            ->raw_query('select eq1.nombre as nombreEq1,eq2.nombre as nombreEq2,eq1.logo as eq1Imagen,eq2.logo as eq2Imagen,reto.fecha,reto.mapa,reto.res_eq_retador as resEq1,reto.res_eq_retado as resEq2 from reto join equipo as eq1 on reto.retador_id=eq1.id join equipo as eq2 on reto.retado_id=eq2.id where reto.ganador IS null AND reto.aceptado = 1 ORDER BY reto.fecha ASC limit 10')
            ->find_many();

        $esCapitan = ORM::for_table('equipo')
            ->where('capitan_id',$_SESSION['usuarioLogin']['id'])
            ->find_one();

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
        $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();
        $_SESSION['retosEquipo'] = $req->compruebaRetosEquipo();

        $app->render('retos.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
            'usuarioLogin'=>$_SESSION['usuarioLogin'],
            'numMensajes' => $_SESSION['numMensajes'],
            'retos1vs1' => $_SESSION['retos1vs1'],
            'retosEquipo' => $_SESSION['retosEquipo'],
            'nuevaSolicitud' => $_SESSION['solicitudes'],
            'ultimosJugados' => $ultJugados,
            'pendientes' => $pendientes,
            'esCapitan' => $esCapitan));
    }


})->name('retos');