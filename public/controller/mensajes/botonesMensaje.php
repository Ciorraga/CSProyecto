<?php

if(isset($_POST['botonRespondeMensaje'])){
    $fecha_actual=date("Y/m/d");
    $titulo = "Re:".$_POST['titulo'];
    $texto = htmlentities($_POST['textoMensaje']);
    $destinatario = htmlentities($_POST['botonRespondeMensaje']);
    $remitente = $_SESSION['usuarioLogin']['id'];

    //var_dump($destinatario);die();

    $nuevoMensaje = ORM::for_table('mensaje')->create();
    $nuevoMensaje->usuario_id = $destinatario;
    $nuevoMensaje->remitente_id = $remitente;
    $nuevoMensaje->asunto = $titulo;
    $nuevoMensaje->mensaje = $texto;
    $nuevoMensaje->fecha = $fecha_actual;
    $nuevoMensaje->save();

    $mensajes = ORM::for_table('mensaje')
        ->inner_join('usuario', array('mensaje.remitente_id', '=', 'usuario.id'))
        ->where('usuario_id',$_SESSION['usuarioLogin']['id'])
        ->find_many();

    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
    $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();

    $app->render('mensajesEntradaUsuario.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
        'mensajeOk' => 'Mensaje enviado con éxito',
        'numMensajes' => $_SESSION['numMensajes'],
        'retos1vs1' => $_SESSION['retos1vs1'],
        'usuarioLogin' => $_SESSION['usuarioLogin'],
        'mensajes' => $mensajes,
        'nuevaSolicitud' => $_SESSION['solicitudes']));
    die();
}

if(isset($_POST['botonMostrar'])){
    $mensajeLeido = ORM::for_table('mensaje')->find_one($_POST['botonMostrar']);
    $mensajeLeido -> leido = 1;
    $mensajeLeido->save();

    $mensaje = ORM::for_table('mensaje')
        ->select('mensaje.id')
        ->select('mensaje.asunto')
        ->select('mensaje.mensaje')
        ->select('mensaje.fecha')
        ->select('usuario.user')
        ->select('mensaje.remitente_id')
        ->inner_join('usuario', array('mensaje.remitente_id', '=', 'usuario.id'))
        ->where('mensaje.id', $_POST['botonMostrar'])
        ->find_array();

    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
    $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();

    $app->render('mensajeEntrada.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
        'mensaje' => $mensaje[0],
        'numMensajes' => $_SESSION['numMensajes'],
        'retos1vs1' => $_SESSION['retos1vs1'],
        'usuarioLogin'=>$_SESSION['usuarioLogin'],
        'nuevaSolicitud' => $_SESSION['solicitudes']));
    die();
}

if(isset($_POST['botonBorrar'])){
    ORM::for_table('mensaje')
        ->find_one($_POST['botonBorrar'])->delete();
    $es_capitan = ORM::for_table('equipo')->where('capitan_id', $_SESSION['usuarioLogin']['id'])->find_one();

    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

    $app->redirect($app->router()->urlFor('entrada'));
    die();
}

if(isset($_POST['botonMostrar_Msg_Salida'])){
    $mensaje = ORM::for_table('mensaje')
        ->select('mensaje.id')
        ->select('mensaje.asunto')
        ->select('mensaje.mensaje')
        ->select('mensaje.fecha')
        ->select('usuario.user')
        ->select('mensaje.remitente_id')
        ->inner_join('usuario', array('mensaje.usuario_id', '=', 'usuario.id'))
        ->where('mensaje.id', $_POST['botonMostrar_Msg_Salida'])
        ->find_array();

    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
    $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();

    $app->render('mensajeSalida.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
        'mensaje' => $mensaje[0],
        'numMensajes' => $_SESSION['numMensajes'],
        'retos1vs1' => $_SESSION['retos1vs1'],
        'usuarioLogin'=>$_SESSION['usuarioLogin'],
        'nuevaSolicitud' => $_SESSION['solicitudes']));
    die();
}

if(isset($_POST['botonBorrar_Msg_Salida'])){
    ORM::for_table('mensaje')
        ->find_one($_POST['botonBorrar_Msg_Salida'])->delete();

    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
    $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();

    $app->redirect($app->router()->urlFor('salida'));
    die();
}

if(isset($_POST['enviarNuevoMensaje'])){
    $asunto = htmlentities($_POST['asunto']);
    $mensaje = htmlentities($_POST['mensaje']);
    $id_usuario= htmlentities($_POST['enviarNuevoMensaje']);
    $id_remitente = $_SESSION['usuarioLogin']['id'];
    $fecha_actual=date("Y/m/d");

    $nuevoMensaje = ORM::for_table('mensaje')->create();
    $nuevoMensaje->usuario_id = $id_usuario;
    $nuevoMensaje->remitente_id = $id_remitente;
    $nuevoMensaje->asunto = $asunto;
    $nuevoMensaje->mensaje = $mensaje;
    $nuevoMensaje->fecha = $fecha_actual;
    $nuevoMensaje->save();

    $mensajes = ORM::for_table('mensaje')
        ->inner_join('usuario', array('mensaje.remitente_id', '=', 'usuario.id'))
        ->where('usuario_id',$_SESSION['usuarioLogin']['id'])
        ->find_many();

    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
    $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();

    $app->render('mensajesEntradaUsuario.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
        'mensajeOk' => 'Mensaje enviado!',
        'retos1vs1' => $_SESSION['retos1vs1'],
        'numMensajes' => $_SESSION['numMensajes'],
        'usuarioLogin' => $_SESSION['usuarioLogin'],
        'mensajes' => $mensajes,
        'nuevaSolicitud' => $_SESSION['solicitudes']));
    die();
}

