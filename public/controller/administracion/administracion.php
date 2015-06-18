<?php

$app->get('/administracion', function() use ($app) {
    if (!isset($_SESSION['usuarioLogin'])) {
        session_destroy();
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }else{
        $req = new comun();
        $notic = $req->mostrarNoticias();

        $app->render('admin/listadoNoticias.html.twig', array('noticias' => $notic));
    }
});

$app->get('/administracion/listanoticias', function() use ($app) {
    if (!isset($_SESSION['usuarioLogin'])) {
        session_destroy();
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }else{
        $req = new comun();
        $notic = $req->mostrarNoticias();

        $app->render('admin/listadoNoticias.html.twig', array('noticias' => $notic));
        die();
    }
})->name("listaNoticias");

$app->get('/administracion/nuevanoticia', function() use ($app) {
    if (!isset($_SESSION['usuarioLogin'])) {
        session_destroy();
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }else{
        $app->render('admin/nuevanoticia.html.twig');
        die();
    }
})->name("nuevaNoticia");

$app->get('/administracion/usuarios', function() use ($app) {
    if (!isset($_SESSION['usuarioLogin'])) {
        session_destroy();
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }else{
        $app->render('admin/listaUsuarios.html.twig');
        die();
    }
})->name("listaUsuarios");




