<?php

$app->get('/clas_us', function() use ($app) {
    if (!isset($_SESSION['usuarioLogin'])) {
        $clasRetos = ORM::for_table('reto')
            ->raw_query('SELECT reto1vs1.retador_id, usuario.user,usuario.id ,count(*) as total, sum(reto1vs1.retador_id=reto1vs1.ganador) as ganados, sum(reto1vs1.retador_id<>reto1vs1.ganador) as perdidos, (sum(reto1vs1.retador_id=reto1vs1.ganador)-sum(reto1vs1.retador_id<>reto1vs1.ganador))/count(*) as ratio FROM reto1vs1 join usuario on usuario.id=reto1vs1.retador_id where reto1vs1.ganador IS NOT null GROUP BY reto1vs1.retador_id ORDER BY ratio')
            ->find_many();
        $app->render('clasificacion1vs1.html.twig',array('clasificacion' => $clasRetos));
    }else{
        $clasRetos = ORM::for_table('reto')
            ->raw_query('SELECT reto1vs1.retador_id, usuario.user,usuario.id ,count(*) as total, sum(reto1vs1.retador_id=reto1vs1.ganador) as ganados, sum(reto1vs1.retador_id<>reto1vs1.ganador) as perdidos, (sum(reto1vs1.retador_id=reto1vs1.ganador)-sum(reto1vs1.retador_id<>reto1vs1.ganador))/count(*) as ratio FROM reto1vs1 join usuario on usuario.id=reto1vs1.retador_id where reto1vs1.ganador IS NOT null GROUP BY reto1vs1.retador_id ORDER BY ratio')
            ->find_many();

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

        $app->render('clasificacion1vs1.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
            'usuarioLogin'=>$_SESSION['usuarioLogin'],
            'numMensajes' => $_SESSION['numMensajes'],
            'nuevaSolicitud' => $_SESSION['solicitudes'],
            'retos1vs1' => $_SESSION['retos1vs1'],
            'clasificacion' => $clasRetos));
    }
});