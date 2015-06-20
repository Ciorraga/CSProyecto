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

        $app->render('usuarios.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
            'usuarioLogin' => $_SESSION['usuarioLogin'],
            'ultimosUsuarios' => $ultUsuarios));
        die();
    }
});