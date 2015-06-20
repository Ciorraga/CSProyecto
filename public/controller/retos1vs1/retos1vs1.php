<?php

$app->get('/retos1vs1', function() use ($app) {
    if (!isset($_SESSION['usuarioLogin'])) {
        $ultJugados = ORM::for_table('reto1vs1')
            ->raw_query('select us1.user as user1,us2.user as user2,us1.imagen as us1Imagen,us2.imagen as us2Imagen,reto1vs1.fecha,reto1vs1.mapa,reto1vs1.res_retador as resUs1,reto.res_retado as resUs2
        from reto1vs1 join usuario as us1 on reto1vs.retador_id=us1.id join usuario as us2 on reto.retado_id=us2.id where reto.ganador IS NOT null ORDER BY reto.fecha DESC limit 10')
            ->find_many();
        var_dump($ultJugados);die();

        $pendientes = ORM::for_table('reto')
            ->raw_query('select eq1.nombre as nombreEq1,eq2.nombre as nombreEq2,eq1.logo as eq1Imagen,eq2.logo as eq2Imagen,reto.fecha,reto.mapa,reto.res_eq_retador as resEq1,reto.res_eq_retado as resEq2
from reto join equipo as eq1 on reto.retador_id=eq1.id join equipo as eq2 on reto.retado_id=eq2.id where reto.ganador IS null AND reto.aceptado IS NOT false ORDER BY reto.fecha ASC limit 10')
            ->find_many();

        $app->render('retos.html.twig',array(
            'ultimosJugados' => $ultJugados,
            'pendientes' => $pendientes));
    }else{
        $ultJugados = ORM::for_table('reto1vs1')
            ->raw_query('select us1.user as user1,us2.user as user2,us1.imagen as us1Imagen,us2.imagen as us2Imagen,reto1vs1.fecha,reto1vs1.mapa,reto1vs1.res_retador as resUs1,reto1vs1.res_retado as resUs2
        from reto1vs1 join usuario as us1 on reto1vs1.retador_id=us1.id join usuario as us2 on reto1vs1.retado_id=us2.id where reto1vs1.ganador IS NOT null ORDER BY reto1vs1.fecha DESC limit 10')
            ->find_many();

        $pendientes = ORM::for_table('reto1vs1')
            ->raw_query('select us1.user as user1,us2.user as user2,us1.imagen as us1Imagen,us2.imagen as us2Imagen,reto1vs1.fecha,reto1vs1.mapa,reto1vs1.res_retador as resUs1,reto1vs1.res_retado as resUs2
                        from reto1vs1 join usuario as us1 on reto1vs1.retador_id=us1.id join usuario as us2 on reto1vs1.retado_id=us2.id where reto1vs1.ganador IS null AND reto1vs1.aceptado IS false ORDER BY reto1vs1.fecha ASC
                limit 10')
            ->find_many();

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

        $app->render('retos1vs1.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
            'usuarioLogin'=>$_SESSION['usuarioLogin'],
            'numMensajes' => $_SESSION['numMensajes'],
            'nuevaSolicitud' => $_SESSION['solicitudes'],
            'ultimosJugados' => $ultJugados,
            'pendientes' => $pendientes,));
    }


})->name('retos1vs1');