<?php

$app->get('/reta_usuario/:id', function ($id) use ($app) {
    $usuario = ORM::for_table('usuario')
        ->where('id',$id)
        ->find_one();

    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
    $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();
    $_SESSION['retosEquipo'] = $req->compruebaRetosEquipo();

    $app->render('retaUsuario.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
        'usuarioLogin'=>$_SESSION['usuarioLogin'],
        'numMensajes' => $_SESSION['numMensajes'],
        'nuevaSolicitud' => $_SESSION['solicitudes'],
        'retosEquipo' => $_SESSION['retosEquipo'],
        'retos1vs1' => $_SESSION['retos1vs1'],
        'usuario' => $usuario));
});

$app->get('/reta_equipo/:id', function ($id) use ($app) {
    if(!isset($_SESSION['usuarioLogin'])){
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }else{
        $esCapitan = ORM::for_table('equipo')
            ->where('capitan_id',$_SESSION['usuarioLogin']['id'])
            ->find_one();

        if($esCapitan){
            $equipo = ORM::for_table('equipo')
                ->where('id',$id)
                ->find_one();

            $req = new comun();
            $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
            $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
            $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();
            $_SESSION['retosEquipo'] = $req->compruebaRetosEquipo();

            $app->render('retaEquipo.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
                'usuarioLogin'=>$_SESSION['usuarioLogin'],
                'numMensajes' => $_SESSION['numMensajes'],
                'retosEquipo' => $_SESSION['retosEquipo'],
                'nuevaSolicitud' => $_SESSION['solicitudes'],
                'retos1vs1' => $_SESSION['retos1vs1'],
                'equipo' => $equipo));
        }else{
            $app->redirect($app->router()->urlFor('inicio'));
            die();
        }
    }

});