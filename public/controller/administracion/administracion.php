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
        $usuarios = ORM::for_table('usuario')
            ->select('usuario.id')
            ->select('usuario.imagen')
            ->select('usuario.user')
            ->select('usuario.nombre')
            ->select('usuario.email')
            ->select('usuario.edad')
            ->find_many();

        $app->render('admin/listaUsuarios.html.twig',array('usuarios' => $usuarios));
        die();
    }
})->name("listaUsuarios");




