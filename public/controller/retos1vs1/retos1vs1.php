<?php

$app->get('/retos1vs1', function() use ($app) {
    if (!isset($_SESSION['usuarioLogin'])) {
        $ultJugados = ORM::for_table('reto1vs1')
            ->raw_query('select us1.user as user1,us2.user as user2,us1.imagen as us1Imagen,us2.imagen as us2Imagen,reto1vs1.fecha,reto1vs1.mapa,reto1vs1.res_retador as resUs1,reto1vs1.res_retado as resUs2
        from reto1vs1 join usuario as us1 on reto1vs1.retador_id=us1.id join usuario as us2 on reto1vs1.retado_id=us2.id where reto1vs1.ganador IS NOT null ORDER BY reto1vs1.fecha DESC limit 10')
            ->find_many();

        $pendientes = ORM::for_table('reto1vs1')
            ->raw_query('select us1.user as user1,us2.user as user2,us1.imagen as us1Imagen,us2.imagen as us2Imagen,reto1vs1.fecha,reto1vs1.mapa,reto1vs1.res_retador as resUs1,reto1vs1.res_retado as resUs2
                        from reto1vs1 join usuario as us1 on reto1vs1.retador_id=us1.id join usuario as us2 on reto1vs1.retado_id=us2.id where reto1vs1.ganador IS null AND reto1vs1.aceptado IS true ORDER BY reto1vs1.fecha ASC
                limit 10')
            ->find_many();

        if($pendientes == null){
            $pendientes="vacio";
        }

        $app->render('retos1vs1.html.twig',array(
            'ultimosJugados' => $ultJugados,
            'pendientes' => $pendientes));
    }else{
        $ultJugados = ORM::for_table('reto1vs1')
            ->raw_query('select us1.user as user1,us2.user as user2,us1.imagen as us1Imagen,us2.imagen as us2Imagen,reto1vs1.fecha,reto1vs1.mapa,reto1vs1.res_retador as resUs1,reto1vs1.res_retado as resUs2
        from reto1vs1 join usuario as us1 on reto1vs1.retador_id=us1.id join usuario as us2 on reto1vs1.retado_id=us2.id where reto1vs1.ganador IS NOT null ORDER BY reto1vs1.fecha DESC limit 10')
            ->find_many();

        $pendientes = ORM::for_table('reto1vs1')
            ->raw_query('select us1.user as user1,us2.user as user2,us1.imagen as us1Imagen,us2.imagen as us2Imagen,reto1vs1.fecha,reto1vs1.mapa,reto1vs1.res_retador as resUs1,reto1vs1.res_retado as resUs2
                        from reto1vs1 join usuario as us1 on reto1vs1.retador_id=us1.id join usuario as us2 on reto1vs1.retado_id=us2.id where reto1vs1.ganador IS null AND reto1vs1.aceptado IS true ORDER BY reto1vs1.fecha ASC
                limit 10')
            ->find_many();
        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
        $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();

        $app->render('retos1vs1.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
            'usuarioLogin'=>$_SESSION['usuarioLogin'],
            'numMensajes' => $_SESSION['numMensajes'],
            'retos1vs1' =>$_SESSION['retos1vs1'],
            'nuevaSolicitud' => $_SESSION['solicitudes'],
            'ultimosJugados' => $ultJugados,
            'pendientes' => $pendientes));
    }
})->name('retos1vs1');

$app->get('/misRetos1vs1', function() use ($app) {
    $pendientes = ORM::for_table('reto1vs1')
        ->join('usuario',array('reto1vs1.retador_id','=','usuario.id'))
        ->select('reto1vs1.id')
        ->select('reto1vs1.fecha')
        ->select('reto1vs1.mapa')
        ->select('usuario.user')
        ->select('usuario.imagen')
        ->where('retado_id',$_SESSION['usuarioLogin']['id'])
        ->where('aceptado',"0")
        ->find_many();
    if($pendientes==null){
        $pendientes="vacio";
    }

    $proximos = ORM::for_table('reto1vs1')
        ->raw_query("select us1.user as user1,us2.user as user2,us1.imagen as us1Imagen,us2.imagen as us2Imagen,reto1vs1.fecha,reto1vs1.mapa,reto1vs1.res_retador as resUs1,reto1vs1.res_retado as resUs2
        from reto1vs1 join usuario as us1 on reto1vs1.retador_id=us1.id join usuario as us2 on reto1vs1.retado_id=us2.id where reto1vs1.ganador IS null AND reto1vs1.aceptado=1  AND reto1vs1.retado_id=". $_SESSION['usuarioLogin']['id']." OR reto1vs1.retador_id=". $_SESSION['usuarioLogin']['id']." ORDER BY reto1vs1.fecha ASC")
        ->find_many();

    $ultJugados = ORM::for_table('reto1vs1')
        ->raw_query("select us1.user as user1,us2.user as user2,us1.imagen as us1Imagen,us2.imagen as us2Imagen,reto1vs1.fecha,reto1vs1.mapa,reto1vs1.res_retador as resUs1,reto1vs1.res_retado as resUs2 from reto1vs1 join usuario as us1 on reto1vs1.retador_id=us1.id join usuario as us2 on reto1vs1.retado_id=us2.id
where reto1vs1.ganador IS NOT null AND reto1vs1.retado_id=". $_SESSION['usuarioLogin']['id']." OR reto1vs1.retador_id=". $_SESSION['usuarioLogin']['id']." ORDER BY reto1vs1.fecha ASC")
        ->find_many();

    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
    $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();

    $app->render('misRetos1vs1.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
        'usuarioLogin'=>$_SESSION['usuarioLogin'],
        'numMensajes' => $_SESSION['numMensajes'],
        'retos1vs1' =>$_SESSION['retos1vs1'],
        'nuevaSolicitud' => $_SESSION['solicitudes'],
        'pendientes' => $pendientes,
        'proximos' => $proximos,
        'ultimosJugados' => $ultJugados
    ));

});

$app->get('/misRetosEquipo', function() use ($app) {
    $es_capitan = ORM::for_table('equipo')->where('capitan_id', $_SESSION['usuarioLogin']['id'])->find_one();
    if($es_capitan){
        $capitan=1;
    }else{
        $pendientes="vacio";
        $capitan = 0;
    }

    $nuevos = ORM::for_table('reto')
        ->join('equipo',array('reto.retador_id','=','equipo.id'))
        ->select('reto.id')
        ->select('reto.fecha')
        ->select('reto.mapa')
        ->select('equipo.nombre')
        ->select('equipo.logo')
        ->where('retado_id',$_SESSION['usuarioLogin']['equipo_id'])
        ->where('aceptado',"0")
        ->find_many();
    if($nuevos==null){
        $nuevos="vacio";
    }

    /*$nuevos = ORM::for_table('reto')
        ->raw_query("select eq1.nombre as nombreEq1,eq2.nombre as nombreEq2,eq1.logo as eq1Imagen,eq2.logo as eq2Imagen,reto.fecha,reto.aceptado,reto.ganador,reto.mapa,reto.res_eq_retador as resEq1,reto.res_eq_retado as resEq2
            from reto join equipo as eq1 on reto.retador_id=eq1.id join equipo as eq2 on reto.retado_id=eq2.id
            where reto.aceptado=0
            AND reto.retado_id=". $_SESSION['usuarioLogin']['equipo_id'] ."
            ORDER BY reto.fecha DESC")
        ->find_many();
    if($nuevos==null){
        $nuevos="vacio";
    }*/

    $proximos = ORM::for_table('reto')
        ->raw_query("select eq1.nombre as nombreEq1,eq2.nombre as nombreEq2,eq1.logo as eq1Imagen,eq2.logo as eq2Imagen,reto.aceptado,reto.fecha,reto.mapa,reto.res_eq_retador as resEq1,reto.res_eq_retado as resEq2
            from reto
            join equipo as eq1 on reto.retador_id=eq1.id
            join equipo as eq2 on reto.retado_id=eq2.id
            where reto.ganador IS null
            AND reto.aceptado = 1
            AND ( reto.retador_id = ". $_SESSION['usuarioLogin']['equipo_id'] ." OR reto.retado_id = ". $_SESSION['usuarioLogin']['equipo_id'] .")
            ORDER BY reto.fecha")
        ->find_many();

    $jugados = ORM::for_table('reto')
        ->raw_query("select eq1.nombre as nombreEq1,eq2.nombre as nombreEq2,eq1.logo as eq1Imagen,eq2.logo as eq2Imagen,reto.aceptado,reto.fecha,reto.mapa,reto.res_eq_retador as resEq1,reto.res_eq_retado as resEq2
            from reto
            join equipo as eq1 on reto.retador_id=eq1.id
            join equipo as eq2 on reto.retado_id=eq2.id
            where reto.ganador IS NOT null
            AND ( reto.retador_id = ". $_SESSION['usuarioLogin']['equipo_id'] ." OR reto.retado_id = ". $_SESSION['usuarioLogin']['equipo_id'] .")
            ORDER BY reto.fecha")
        ->find_many();


    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
    $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();

    $app->render('misRetosEquipo.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
        'usuarioLogin'=>$_SESSION['usuarioLogin'],
        'numMensajes' => $_SESSION['numMensajes'],
        'retos1vs1' =>$_SESSION['retos1vs1'],
        'nuevaSolicitud' => $_SESSION['solicitudes'],
        'pendientes' => $nuevos,
        'proximos' => $proximos,
        'capitan' => $capitan,
        'ultimosJugados' => $jugados
    ));

});
