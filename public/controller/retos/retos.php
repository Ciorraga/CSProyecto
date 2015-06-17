<?php

$app->get('/retos', function() use ($app) {
    //SELECT retador_id, count(*), sum(retador_id=ganador), sum(retador_id<>ganador) FROM reto GROUP BY retador_id
    $ultJugados = ORM::for_table('reto')
        ->raw_query('select eq1.nombre as nombreEq1,eq2.nombre as nombreEq2,eq1.logo as eq1Imagen,eq2.logo as eq2Imagen,reto.fecha,reto.mapa,reto.res_eq_retador as resEq1,reto.res_eq_retado as resEq2 from reto join equipo as eq1 on reto.retador_id=eq1.id join equipo as eq2 on reto.retado_id=eq2.id where reto.ganador IS NOT null ORDER BY reto.fecha DESC limit 10')
        ->find_many();

    $pendientes = ORM::for_table('reto')
        ->raw_query('select eq1.nombre as nombreEq1,eq2.nombre as nombreEq2,eq1.logo as eq1Imagen,eq2.logo as eq2Imagen,reto.fecha,reto.mapa,reto.res_eq_retador as resEq1,reto.res_eq_retado as resEq2 from reto join equipo as eq1 on reto.retador_id=eq1.id join equipo as eq2 on reto.retado_id=eq2.id where reto.ganador IS null AND reto.aceptado IS NOT false ORDER BY reto.fecha ASC limit 10')
        ->find_many();

    $esCapitan = ORM::for_table('equipo')
        ->where('capitan_id',$_SESSION['usuarioLogin']['id'])
        ->find_one();

    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

    $app->render('retos.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'nuevaSolicitud' => $_SESSION['solicitudes'],'ultimosJugados' => $ultJugados,'pendientes' => $pendientes,'esCapitan' => $esCapitan));
})->name('retos');