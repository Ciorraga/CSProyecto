<?php

$app->get('/clas_eq', function() use ($app) {
    if (!isset($_SESSION['usuarioLogin'])) {
        $clasRetos = ORM::for_table('reto')
            ->raw_query('SELECT reto.retador_id, equipo.nombre,equipo.id ,count(*) as total, sum(reto.retador_id=reto.ganador) as ganados, sum(reto.retador_id<>reto.ganador) as perdidos, (sum(reto.retador_id=reto.ganador)-sum(reto.retador_id<>reto.ganador))/count(*) as ratio FROM reto join equipo on equipo.id=reto.retador_id where reto.ganador IS NOT null GROUP BY reto.retador_id ORDER BY ratio')
            ->find_many();

        $app->render('clasificacionEquipos.html.twig',array('clasificacion' => $clasRetos));
    }else{
        $clasRetos = ORM::for_table('reto')
            ->raw_query('SELECT reto.retador_id, equipo.nombre,equipo.id ,count(*) as total, sum(reto.retador_id=reto.ganador) as ganados, sum(reto.retador_id<>reto.ganador) as perdidos, (sum(reto.retador_id=reto.ganador)-sum(reto.retador_id<>reto.ganador))/count(*) as ratio FROM reto join equipo on equipo.id=reto.retador_id where reto.ganador IS NOT null GROUP BY reto.retador_id ORDER BY ratio')
            ->find_many();

        $esCapitan = ORM::for_table('equipo')
            ->where('capitan_id',$_SESSION['usuarioLogin']['id'])
            ->find_one();

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

        $app->render('clasificacionEquipos.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
            'usuarioLogin'=>$_SESSION['usuarioLogin'],
            'numMensajes' => $_SESSION['numMensajes'],
            'nuevaSolicitud' => $_SESSION['solicitudes'],
            'clasificacion' => $clasRetos,
            'esCapitan' => $esCapitan));
    }
});