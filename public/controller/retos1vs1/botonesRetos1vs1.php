<?php

if(isset($_POST['botonAceptarReto1vs1'])){
    $reto = ORM::for_table('reto1vs1')
        ->where('id',$_POST['botonAceptarReto1vs1'])
        ->find_one();
    $reto->aceptado = '1';
    $reto->save();

    $req = new comun();
    $notic = $req->mostrarNoticias();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
    $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();
    $app->render('inicio.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
        'nuevaSolicitud' => $_SESSION['solicitudes'],
        'noticias' => $notic,
        'numMensajes' => $_SESSION['numMensajes'],
        'usuarioLogin' => $_SESSION['usuarioLogin'],
        'retos1vs1' => $_SESSION['retos1vs1'],
        'registrado' => 'env',
        'retos1vs1' =>$_SESSION['retos1vs1'],
        'mensajeOk' => 'El reto fué aceptado'));
}

if(isset($_POST['botonRechazarReto1vs1'])){
    $reto = ORM::for_table('reto1vs1')
        ->where('id',$_POST['botonRechazarReto1vs1'])
        ->find_one();
    $reto->delete();

    $req = new comun();
    $notic = $req->mostrarNoticias();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
    $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();
    $app->render('inicio.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
        'nuevaSolicitud' => $_SESSION['solicitudes'],
        'noticias' => $notic,
        'numMensajes' => $_SESSION['numMensajes'],
        'usuarioLogin' => $_SESSION['usuarioLogin'],
        'retos1vs1' => $_SESSION['retos1vs1'],
        'registrado' => 'env',
        'mensajeOk' => 'El reto ha sido rechazado'));
}