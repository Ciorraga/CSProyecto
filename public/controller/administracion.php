<?php

$app->get('/administracion', function() use ($app) {
    if($_SESSION['usuarioLogin']['es_admin']==0){
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }
    $app->redirect($app->router()->urlFor('listaNoticias'));
    die();
});

$app->get('/administracion/listanoticias', function() use ($app) {
    $req = new comun();
    $notic = $req->mostrarNoticias();

    $app->render('admin/listadoNoticias.html.twig', array('noticias' => $notic));
    die();
})->name("listaNoticias");

