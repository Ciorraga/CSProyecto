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

$app->get('/administracion/retos', function() use ($app) {
    if (!isset($_SESSION['usuarioLogin'])) {
        session_destroy();
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }else{
        $retosEq = ORM::for_table('reto')
            ->raw_query('select eq1.nombre as nombreEq1,eq2.nombre as nombreEq2,eq1.logo as eq1Imagen,eq2.logo as eq2Imagen,reto.id,reto.fecha,reto.mapa,reto.res_eq_retador as resEq1,reto.res_eq_retado as resEq2 from reto join equipo as eq1 on reto.retador_id=eq1.id join equipo as eq2 on reto.retado_id=eq2.id where reto.ganador IS null AND reto.aceptado IS NOT false ORDER BY reto.fecha ASC limit 10')
            ->find_many();
        $retos1vs1 = 23;

        $app->render('admin/listaRetos.html.twig',array('retosEq' => $retosEq));
        die();
    }
})->name("listaRetos");

$app->get('/retoActualiza/:id', function ($id) {
        $equipos = ORM::for_table('reto')
            ->where('id',$id)
            ->find_one();

        echo "<form action='/' method='POST'>";
            echo "<span style='margin-left:25px;'>Retador: <input type='text' name='resRetador'/></span>" ;
            echo "<span style='margin-left:5px;'>Retado: <input type='text' name='resRetado'/></span>" ;
            echo "<button style='float:right;' type='submit' class='btn btn-success' name='fijaResultadoReto' value='".$id."'>Actualizar</button>";
        echo "</form>";


})->name('buscarEquipo');




