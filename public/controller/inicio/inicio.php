<?php

//Página de inicio de la aplicación
$app->get('/', function() use ($app) {
    //Si es un usaurio NO registrado
    if (!isset($_SESSION['usuarioLogin'])) {
        unset($_SESSION);
        session_destroy();

        $req = new comun();
        $notic = $req->mostrarNoticias();
        $app->render('inicio.html.twig',array('noticias' => $notic));
    } else { //Si es un usaurio registrado
        $req = new comun();
        $notic = $req->mostrarNoticias();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
        $app->render('inicio.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'nuevaSolicitud' => $_SESSION['solicitudes'],'noticias' => $notic,'numMensajes' => $_SESSION['numMensajes'],'usuarioLogin' => $_SESSION['usuarioLogin'],'registrado' => 'env'));
    }
})->name('inicio');