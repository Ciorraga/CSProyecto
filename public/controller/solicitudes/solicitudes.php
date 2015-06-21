<?php

$app->get('/solicitudes', function() use ($app) {
    if(!isset($_SESSION['usuarioLogin'])){
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }
    $compCapitan = ORM::for_table('equipo')
        ->where('capitan_id',$_SESSION['usuarioLogin']['id'])
        ->find_one();

    if(!$compCapitan){
        $app->redirect($app->router()->urlFor('equipos'));
        die();
    }else{
        $solicitudes = ORM::for_table('equipo_usuario')
            ->select('equipo_usuario.id')
            ->select('usuario.user')
            ->select('usuario.imagen')
            ->select('usuario.nombre')
            ->select('usuario.steam')
            ->select('equipo_usuario.estado')
            ->join('usuario', array('equipo_usuario.usuario_id', '=', 'usuario.id'))
            ->where('equipo_usuario.equipo_id', $_SESSION['usuarioLogin']['equipo_id'])
            ->find_many();


        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
        $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();
        $_SESSION['retosEquipo'] = $req->compruebaRetosEquipo();

        $app->render('solicitudes.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
            'usuarioLogin'=>$_SESSION['usuarioLogin'],
            'numMensajes' => $_SESSION['numMensajes'],
            'retosEquipo' => $_SESSION['retosEquipo'],
            'retos1vs1' =>$_SESSION['retos1vs1'],
            'nuevaSolicitud' => $_SESSION['solicitudes'],
            'solicitudes' => $solicitudes));
    }
})->name('solicitudes');