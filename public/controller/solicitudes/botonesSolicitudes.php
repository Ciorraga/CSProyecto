<?php

//Al pulsar el boton aceptar del menú solicitudes del capitán de equipo
if(isset($_POST['botonAceptarSol'])){
    //var_dump($_POST['botonAceptarSol']);die();
    $usuario = ORM::for_table('usuario')
        ->select('usuario.equipo_id')
        ->join('equipo_usuario', array('equipo_usuario.usuario_id', '=', 'usuario.id'))
        ->where('equipo_usuario.id',$_POST['botonAceptarSol'])
        ->find_one();

    if($usuario['equipo_id']==""){
        $us = ORM::for_table('equipo_usuario')
            ->where('id',$_POST['botonAceptarSol'])
            ->find_one();

        $userAModificar = ORM::for_table('usuario')
            ->where('id',$us['usuario_id'])
            ->find_one();
        $userAModificar->equipo_id = $_SESSION['usuarioLogin']['equipo_id'];
        $userAModificar->save();

        $modificaEstadoPeticion = ORM::for_table('equipo_usuario')
            ->where('id',$_POST['botonAceptarSol'])
            ->find_one();
        $modificaEstadoPeticion->estado = 'aprobada';
        $modificaEstadoPeticion->save();


        $solicitudes = ORM::for_table('equipo_usuario')
            ->join('usuario', array('equipo_usuario.usuario_id', '=', 'usuario.id'))
            ->where('equipo_usuario.equipo_id', $_SESSION['usuarioLogin']['equipo_id'])
            ->where('equipo_usuario.estado','pendiente')
            ->find_many();

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
        $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();
        $_SESSION['retosEquipo'] = $req->compruebaRetosEquipo();

        $solicitudes = ORM::for_table('equipo_usuario')
            ->select('equipo_usuario.id')
            ->select('usuario.user')
            ->select('usuario.imagen')
            ->select('usuario.nombre')
            ->select('usuario.steam')
            ->select('equipo_usuario.estado')
            ->join('usuario', array('equipo_usuario.usuario_id', '=', 'usuario.id'))
            ->where('equipo_usuario.equipo_id', $_SESSION['usuarioLogin']['equipo_id'])
            ->find_many();

        $app->render('solicitudes.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
            'usuarioLogin'=>$_SESSION['usuarioLogin'],
            'numMensajes' => $_SESSION['numMensajes'],
            'retosEquipo' => $_SESSION['retosEquipo'],
            'nuevaSolicitud' => $_SESSION['solicitudes'],
            'retos1vs1' =>$_SESSION['retos1vs1'],
            'solicitudes' => $solicitudes,'aprobada' => 'ok',
            'mensajeOk' => "Solicitud aprobada con éxito"));
    }else{

        $modificaEstadoPeticion = ORM::for_table('equipo_usuario')
            ->where('id',$_POST['botonAceptarSol'])
            ->find_one();
        $modificaEstadoPeticion->estado = 'denegada';
        $modificaEstadoPeticion->save();

        $solicitudes = ORM::for_table('equipo_usuario')
            ->select('equipo_usuario.id')
            ->select('usuario.user')
            ->select('usuario.imagen')
            ->select('usuario.nombre')
            ->select('usuario.steam')
            ->select('equipo_usuario.estado')
            ->join('usuario', array('equipo_usuario.usuario_id', '=', 'usuario.id'))
            ->where('equipo_usuario.equipo_id', $_SESSION['usuarioLogin']['equipo_id'])
            ->find_many();

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
        $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();
        $_SESSION['retosEquipo'] = $req->compruebaRetosEquipo();

        $app->render('solicitudes.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
            'usuarioLogin'=>$_SESSION['usuarioLogin'],
            'numMensajes' => $_SESSION['numMensajes'],
            'retosEquipo' => $_SESSION['retosEquipo'],
            'retos1vs1' =>$_SESSION['retos1vs1'],
            'nuevaSolicitud' => $_SESSION['solicitudes'],
            'solicitudes' => $solicitudes,'tieneEquipo' => 'ok'));
    }
}

//Al pulsar el boton denegar del menú solicitudes del capitán de equipo
if(isset($_POST['botonDenegarSol'])){
    $us = ORM::for_table('equipo_usuario')
        ->where('id',$_POST['botonDenegarSol'])
        ->find_one();

    $modificaEstadoPeticion = ORM::for_table('equipo_usuario')
        ->where('id',$_POST['botonDenegarSol'])
        ->find_one();
    $modificaEstadoPeticion->estado = 'denegada';
    $modificaEstadoPeticion->save();


    $solicitudes = ORM::for_table('equipo_usuario')
        ->select('equipo_usuario.id')
        ->select('usuario.user')
        ->select('usuario.imagen')
        ->select('usuario.nombre')
        ->select('usuario.steam')
        ->select('equipo_usuario.estado')
        ->join('usuario', array('equipo_usuario.usuario_id', '=', 'usuario.id'))
        ->where('equipo_usuario.equipo_id', $_SESSION['usuarioLogin']['equipo_id'])
        ->find_many();

    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
    $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();
    $_SESSION['retosEquipo'] = $req->compruebaRetosEquipo();

    $app->render('solicitudes.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
        'retosEquipo' => $_SESSION['retosEquipo'],
        'usuarioLogin'=>$_SESSION['usuarioLogin'],
        'numMensajes' => $_SESSION['numMensajes'],
        'nuevaSolicitud' => $_SESSION['solicitudes'],
        'solicitudes' => $solicitudes,'aprobada' => 'notOk','mensajeError' => 'Solicitud denegada con éxito'));
}