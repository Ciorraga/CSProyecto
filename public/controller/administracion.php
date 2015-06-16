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

$app->get('/administracion/nuevanoticia', function() use ($app) {
    $app->render('admin/nuevanoticia.html.twig');
    die();
})->name("listaNoticias");

/*-----------------------------------------BOTONES---------------------------------------*/

$app->post('/', function() use ($app) {
    if(isset($_POST['botonBorrarNoticia'])){
        $req = new comun();
        $notic = $req->mostrarNoticias();
        ORM::for_table('noticia')
            ->find_one($_POST['botonBorrarNoticia'])->delete();

        $app->redirect($app->router()->urlFor('listaNoticias'));
        die();
    }
});

$app->post('/', function() use ($app) {
    if(isset($_POST)){
        echo "Entra";die();
        $app->redirect($app->router()->urlFor('listaNoticias'));
        die();
    }
});


