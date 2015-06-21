<?php

$app->get('/usuarios', function() use ($app) {
    if (!isset($_SESSION['usuarioLogin'])) {
        session_destroy();
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }else {
        $ultUsuarios = ORM::for_table('usuario')
            ->select('usuario.id')
            ->select('usuario.imagen')
            ->select('usuario.user')
            ->select('usuario.edad')
            ->select('usuario.steam')
            ->limit(10)
            ->order_by_desc('id')
            ->find_many();

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
        $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();
        $_SESSION['retosEquipo'] = $req->compruebaRetosEquipo();

        $app->render('usuarios.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
            'retos1vs1' =>$_SESSION['retos1vs1'],
            'retosEquipo' => $_SESSION['retosEquipo'],
            'usuarioLogin' => $_SESSION['usuarioLogin'],
            'ultimosUsuarios' => $ultUsuarios));
        die();
    }
})->name('usuarios');