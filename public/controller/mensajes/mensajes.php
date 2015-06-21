<?php

//Sección bandeja de entrada de MENSAJES de cada usuario
$app->get('/entrada', function() use ($app) {
    if(!isset($_SESSION['usuarioLogin'])){
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }
    $mensajes = ORM::for_table('mensaje')
        ->select('mensaje.id')
        ->select('mensaje.leido')
        ->select('mensaje.asunto')
        ->select('mensaje.mensaje')
        ->select('mensaje.fecha')
        ->select('usuario.user')
        ->join('usuario', array('mensaje.remitente_id', '=', 'usuario.id'))
        ->where('mensaje.usuario_id',$_SESSION['usuarioLogin']['id'])
        ->order_by_desc('mensaje.fecha')
        ->find_array();

    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
    $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();
    $_SESSION['retosEquipo'] = $req->compruebaRetosEquipo();

    $app->render('mensajesEntradaUsuario.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
        'mensajes' => $mensajes,
        'usuarioLogin'=>$_SESSION['usuarioLogin'],
        'numMensajes' => $_SESSION['numMensajes'],
        'retos1vs1' => $_SESSION['retos1vs1'],
        'retosEquipo' => $_SESSION['retosEquipo'],
        'nuevaSolicitud' => $_SESSION['solicitudes']));
    die();
})->name('entrada');

//Sección bandeja de salida de MENSAJES de cada usuario
$app->get('/salida', function() use ($app) {
    if(!isset($_SESSION['usuarioLogin'])){
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }
    $mensajes = ORM::for_table('mensaje')
        ->select('mensaje.asunto')
        ->select('mensaje.fecha')
        ->select('mensaje.id')
        ->select('usuario.user')
        ->inner_join('usuario', array('mensaje.usuario_id', '=', 'usuario.id'))
        ->where('remitente_id',$_SESSION['usuarioLogin']['id'])
        ->find_many();
    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
    $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();
    $_SESSION['retosEquipo'] = $req->compruebaRetosEquipo();

    $app->render('mensajesSalidaUsuario.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
        'mensajes' => $mensajes,
        'retos1vs1' => $_SESSION['retos1vs1'],
        'retosEquipo' => $_SESSION['retosEquipo'],
        'usuarioLogin'=>$_SESSION['usuarioLogin'],
        'numMensajes' => $_SESSION['numMensajes'],
        'nuevaSolicitud' => $_SESSION['solicitudes']));
    die();
})->name('salida');

$app->get('/nuevoMensaje', function() use ($app) {
    if(!isset($_SESSION['usuarioLogin'])){
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }
    $usuarios = ORM::for_table('usuario')
        ->select('user')
        ->select('id')
        ->find_many();
    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
    $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();
    $_SESSION['retosEquipo'] = $req->compruebaRetosEquipo();

    $app->render('mensajeNuevo.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
        'usuarioLogin'=>$_SESSION['usuarioLogin'],
        'numMensajes' => $_SESSION['numMensajes'],
        'retosEquipo' => $_SESSION['retosEquipo'],
        'retos1vs1' => $_SESSION['retos1vs1'],
        'usuarios' => $usuarios,
        'nuevaSolicitud' => $_SESSION['solicitudes']));
})->name('nuevoMensaje');



$app->get('/buscarUsuario/:id', function ($id) {
    $usuario = ORM::for_table('usuario')
        ->where_like('user', '%'. $id .'%')
        ->find_many();

    foreach ($usuario as $valor){
        echo "<a href='/mensajeNuevo/".$valor['id']."'>". $valor['user'] ."</a><br/>" ;
    };
})->name('buscarUsuario');

$app->get('/mensajeNuevo/:id', function ($id) use ($app) {
    if(!isset($_SESSION['usuarioLogin'])){
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }

    $cons = ORM::for_table('usuario')
        ->where('id', $id)
        ->find_one();

    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
    $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();
    $_SESSION['retosEquipo'] = $req->compruebaRetosEquipo();

    $app->render('mensajeNuevo.html.twig',array('usuarioMensaje' => $cons ,
        'imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
        'retosEquipo' => $_SESSION['retosEquipo'],
        'retos1vs1' => $_SESSION['retos1vs1'],
        'usuarioLogin'=>$_SESSION['usuarioLogin'],
        'numMensajes' => $_SESSION['numMensajes'],
        'nuevaSolicitud' => $_SESSION['solicitudes']));
    die();
});