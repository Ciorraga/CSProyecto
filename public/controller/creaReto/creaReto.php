<?php

$app->get('/reta_usuario/:id', function ($id) use ($app) {
    $usuario = ORM::for_table('usuario')
        ->where('id',$id)
        ->find_one();

    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

    $app->render('retaUsuario.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
        'usuarioLogin'=>$_SESSION['usuarioLogin'],
        'numMensajes' => $_SESSION['numMensajes'],
        'nuevaSolicitud' => $_SESSION['solicitudes'],
        'usuario' => $usuario));
});