<?php

if(isset($_POST['botonEnviaReto'])){
    if($_POST['fecha']==""){
        $ultUsuarios = ORM::for_table('usuario')
            ->select('usuario.id')
            ->select('usuario.imagen')
            ->select('usuario.user')
            ->select('usuario.edad')
            ->select('usuario.steam')
            ->limit(10)
            ->order_by_desc('id')
            ->find_many();

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
        $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();

        $app->render('usuarios.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
            'usuarioLogin' => $_SESSION['usuarioLogin'],
            'ultimosUsuarios' => $ultUsuarios,
            'mensajeError' => "Tiene que adjuntar una fecha cuando haga un reto"));
        die();
    }
    $compReto = ORM::for_table('reto1vs1')
        ->where('retador_id',$_SESSION['usuarioLogin']['id'])
        ->where('retado_id',$_POST['botonEnviaReto'])
        ->where('ganador',"0")
        ->find_one();

    if($compReto){
        $ultUsuarios = ORM::for_table('usuario')
            ->select('usuario.id')
            ->select('usuario.imagen')
            ->select('usuario.user')
            ->select('usuario.edad')
            ->select('usuario.steam')
            ->limit(10)
            ->order_by_desc('id')
            ->find_many();

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

        $app->render('usuarios.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
            'usuarioLogin' => $_SESSION['usuarioLogin'],
            'ultimosUsuarios' => $ultUsuarios,
            'retos1vs1' => $_SESSION['retos1vs1'],
            'mensajeError' => 'El usuario seleccionado y tú aún tenéis un reto activo. Termina el reto, o ponte en contacto con un administrador'));
        die();
    }else{
        $nuevoReto1vs1 = ORM::for_table('reto1vs1')
            ->create();
        $nuevoReto1vs1->retador_id = $_SESSION['usuarioLogin']['id'];
        $nuevoReto1vs1->retado_id = $_POST['botonEnviaReto'];
        $nuevoReto1vs1->fecha = $_POST['fecha'].":00";
        $nuevoReto1vs1->mapa = $_POST['mapa'];
        $nuevoReto1vs1->save();

        $ultUsuarios = ORM::for_table('usuario')
            ->select('usuario.id')
            ->select('usuario.imagen')
            ->select('usuario.user')
            ->select('usuario.edad')
            ->select('usuario.steam')
            ->limit(10)
            ->order_by_desc('id')
            ->find_many();

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
        $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();

        $app->render('usuarios.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
            'usuarioLogin' => $_SESSION['usuarioLogin'],
            'ultimosUsuarios' => $ultUsuarios,
            'retos1vs1' => $_SESSION['retos1vs1'],
            'mensajeOk' => 'Reto enviado!'));

        die();
    }

}