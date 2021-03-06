<?php

//Cuando el usuario pulsa en "Equipos"
$app->get('/equipos', function() use ($app) {
    if(!isset($_SESSION['usuarioLogin'])){
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }
    $_SESSION['usuarioLogin'] = ORM::for_table('usuario')
        ->where('id',$_SESSION['usuarioLogin']['id'])
        ->find_one();
    //Si el usuario NO tiene equipo
    if($_SESSION['usuarioLogin']['equipo_id']==null){
        $equipo = null;
        $req = new comun();
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
        $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();
        $_SESSION['retosEquipo'] = $req->compruebaRetosEquipo();

        $app->render('equipos.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
            'usuarioLogin'=>$_SESSION['usuarioLogin'],
            'numMensajes' => $_SESSION['numMensajes'],
            'retosEquipo' => $_SESSION['retosEquipo'],
            'retos1vs1' => $_SESSION['retos1vs1'],
            'nuevaSolicitud' => $_SESSION['solicitudes']));
    }else{
        //Consulta para extraer los datos del equipo
        $equipo = ORM::for_table('equipo')
            ->where('id',$_SESSION['usuarioLogin']['equipo_id'])
            ->find_many();
        //Consulta para extraer los datos de los miembros del equipo
        $usuarios = ORM::for_table('usuario')
            ->where('equipo_id',$equipo[0]['id'])
            ->find_many();

        $ultJugados = ORM::for_table('reto')
            ->raw_query('select reto.ganador,eq1.nombre as nombreEq1,eq2.nombre as nombreEq2,eq1.logo as eq1Imagen,eq2.logo as eq2Imagen,reto.fecha,reto.mapa,reto.res_eq_retador as resEq1,reto.res_eq_retado as resEq2 from reto join equipo as eq1 on reto.retador_id=eq1.id join equipo as eq2 on reto.retado_id=eq2.id where (reto.retador_id='.$_SESSION['usuarioLogin']['equipo_id'].' or reto.retado_id='.$_SESSION['usuarioLogin']['equipo_id'].') and reto.ganador IS NOT NULL  ORDER BY reto.fecha DESC')
            ->find_many();

        if($_SESSION['usuarioLogin']['id']==$equipo[0]['id']){
            $miEquipo = false;
        }else{
            $miEquipo = true;
        }

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
        $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();
        $_SESSION['retosEquipo'] = $req->compruebaRetosEquipo();

        $app->render('equipos.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
            'usuarioLogin'=>$_SESSION['usuarioLogin'],
            'numMensajes' => $_SESSION['numMensajes'],
            'equipo' => $equipo,
            'usuarios' => $usuarios,
            'retosEquipo' => $_SESSION['retosEquipo'],
            'miEquipo' => $miEquipo,
            'nuevaSolicitud' => $_SESSION['solicitudes'],
            'retos1vs1' => $_SESSION['retos1vs1'],
            'misRetos' => $ultJugados));
        die();
    }
})->name('equipos');

$app->get('/buscarEquipo/:id', function ($id) {
    $equipo = ORM::for_table('equipo')
        ->where_like('nombre', '%'. $id .'%')
        ->find_many();

    foreach ($equipo as $valor){
        echo "<a href='/equipos/".$valor['nombre']."'>". $valor['nombre'] ."</a><br/>" ;
    };
})->name('buscarEquipo');

$app->get('/equipos/:equipo', function ($equipo) use ($app) {
    if(!isset($_SESSION['usuarioLogin'])){
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }

    $equipoUsuarioActual = ORM::for_table('equipo')
        ->where('capitan_id',$_SESSION['usuarioLogin']['id'])
        ->find_one();

    $eq = ORM::for_table('equipo')
        ->where('nombre',$equipo)
        ->find_many();

    $usuarios = ORM::for_table('usuario')
        ->where('equipo_id',$eq[0]['id'])
        ->find_many();

    if(!$_SESSION['usuarioLogin']['equipo_id']){
        $botonSolicitud = [true,$eq[0]['id']];
    }else{
        $botonSolicitud = false;
    }

    if($_SESSION['usuarioLogin']['equipo_id']==$eq[0]['id']){
        $miEquipo = true;
    }else{
        $miEquipo = false;
    }

    $ultJugados = ORM::for_table('reto')
        ->raw_query('select reto.ganador,eq1.nombre as nombreEq1,eq2.nombre as nombreEq2,eq1.logo as eq1Imagen,eq2.logo as eq2Imagen,reto.fecha,reto.mapa,reto.res_eq_retador as resEq1,reto.res_eq_retado as resEq2 from reto join equipo as eq1 on reto.retador_id=eq1.id join equipo as eq2 on reto.retado_id=eq2.id where (reto.retador_id='.$eq[0]['id'].' or reto.retado_id='.$eq[0]['id'].') and reto.ganador IS NOT NULL  ORDER BY reto.fecha DESC')
        ->find_many();

    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
    $imagenUser = $_SESSION['usuarioLogin']['imagen'];
    $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();
    $_SESSION['retosEquipo'] = $req->compruebaRetosEquipo();

    $app->render('equipos.html.twig',array('imagenUser'=>$imagenUser,
        'usuarioLogin'=>$_SESSION['usuarioLogin'],
        'numMensajes' => $_SESSION['numMensajes'],
        'equipo' => $eq,
        'miEquipo' => $miEquipo,
        'usuarios' => $usuarios,
        'retosEquipo' => $_SESSION['retosEquipo'],
        'botonSolicitud' => $botonSolicitud,
        'nuevaSolicitud' => $_SESSION['solicitudes'],
        'misRetos' => $ultJugados,
        'retos1vs1' => $_SESSION['retos1vs1'],
        'equipoUsuario' => $equipoUsuarioActual));
});